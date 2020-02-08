#!/usr/bin/env python
'''
    Copyright (C) 2020 xtr4nge [_AT_] gmail.com

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
'''

import time
import sys, getopt
import traceback
from multiprocessing import Process
import signal
import threading

from scapy.all import *

observedclients = []

# ------- MENU -------
def usage():
    print "\nscan-ap 1.0 by xtr4nge"
    
    print "Usage: scan-ap.py <options>\n"
    print "Options:"
    print "-i <i>, --interface=<i>                  set interface (default: mon0)"
    print "-t <type>, --type=<type>                 type filter (default: 0)"
    print "-s <subtype> --subtype=<subtype>         subtypes filters (default: 0,1,2,3,4,5,8)"
    print "-d <delay>, --delay=<delay>              write delay (default: 2s)"
    print "-l <log>, --log=<log>                    log file path"
    print "-c <channel>, --channel=<channel>        channel hopping (default: 1,2,3,4,5,...13)"
    print "-h                                       Print this help message."
    print ""
    print "Author: xtr4nge"
    print ""

def parseOptions(argv):
    INTERFACE = "mon0" # server, minion
    TYPE =  "0,1"
    LOG = ""
    SUBTYPE = "0,1,2,3,4,5,8,10,11,12,13"
    DELAY = 2
    CHANNEL = "1,2,3,4,5,6,7,8,9,10,11,12,13"

    try:
        opts, args = getopt.getopt(argv, "hi:t:l:s:d:c:",
                                   ["help", "interface=", "type=", "log=", "subtype=", "delay=", "channel="])

        for opt, arg in opts:
            if opt in ("-h", "--help"):
                usage()
                sys.exit()
            elif opt in ("-i", "--interface"):
                INTERFACE = arg
            elif opt in ("-t", "--type"):
                TYPE = arg
            elif opt in ("-l", "--log"):
                LOG = arg
                with open(LOG, 'w') as f:
                    f.write("")
            elif opt in ("-s", "--subtype"):
                SUBTYPE = arg
            elif opt in ("-d", "--delay"):
                DELAY = int(arg)
            elif opt in ("-c", "--channel"):
                CHANNEL = arg
        
        # TYPE INTO INT ARRAY
        TEMP = CHANNEL.split(",")
        CHANNEL = []
        for i in TEMP:
            CHANNEL.append(int(i))
        
        # TYPE INTO INT ARRAY
        TEMP = TYPE.split(",")
        TYPE = []
        for i in TEMP:
            TYPE.append(int(i))

        # SUBTYPE INTO INT ARRAY
        TEMP = SUBTYPE.split(",")
        SUBTYPE = []
        for i in TEMP:
            SUBTYPE.append(int(i))

        return (INTERFACE, TYPE, LOG, SUBTYPE, DELAY, CHANNEL)
                    
    except getopt.GetoptError:           
        usage()
        sys.exit(2) 

# -------------------------
# GLOBAL VARIABLES
# -------------------------
desc = {
        0: "Association Request", 
        1: "Association Response",
        2: "Reassociation Request",
        3: "Reassociation Response",
        4: "Probe Request",
        5: "Probe Response",
        8: "Beacon Frame",
        9: "ATIM",
        10: "Disassociation",
        11: "Authentication",
        12: "Deauthentication",
        13: "Acknowledgement",
    }

INVENTORY = {}
EXCLUDE = [None, "00:00:00:00:00:00", "ff:ff:ff:ff:ff:ff"]
aps = {}
(INTERFACE, TYPE, LOG, SUBTYPE, DELAY, CHANNEL) = parseOptions(sys.argv[1:])

init_delay_time = int(time.time())

# -------------------------
# SNIFFER
# -------------------------
def sniffmgmt(p):
    global TYPE
    global SUBTYPE
    global LOG
    global DELAY
    global init_delay_time
    
    IP = []
    
    try:
        
        SIGNAL = -(256-ord(p.notdecoded[-4:-3]))
        #p = pkt[Dot11Elt]
        cap = p.sprintf("{Dot11Beacon:%Dot11Beacon.cap%}"
                          "{Dot11ProbeResp:%Dot11ProbeResp.cap%}").split('+')
        
        BSSID = ""
        SSID, CHANNEL = None, None
        crypto = []
        pDot11Elt = None
        CLIENT = ""
        
        try: pDot11Elt= p[Dot11Elt]
        except: pass
        while isinstance(pDot11Elt, Dot11Elt):
            BSSID = p[Dot11].addr3

            if pDot11Elt.ID == 0:
                SSID = p.info
            elif pDot11Elt.ID == 3:
                CHANNEL = ord(pDot11Elt.info)
            elif pDot11Elt.ID == 48:
                crypto.append("WPA2")
            elif pDot11Elt.ID == 221 and pDot11Elt.info.startswith('\x00P\xf2\x01\x01\x00'):
                crypto.append("WPA")
            pDot11Elt = pDot11Elt.payload
        
        if SSID != None and SSID != "" and BSSID not in aps and BSSID not in EXCLUDE:
            print BSSID, SSID, CHANNEL, crypto, SIGNAL
            aps[BSSID] = [SSID, CHANNEL, crypto, SIGNAL]
        elif BSSID in aps:
            aps[BSSID][3] = SIGNAL
        
        IP.append(str(p.addr1))
        IP.append(str(p.addr2))
        IP.append(str(p.addr3))
        
        if p.subtype in SUBTYPE: # DEBUG
            pass
            #print p.type, p.subtype, IP, desc[p.subtype], SSID, CHANNEL, SIGNAL
        #return
        
        # TYPE = 0
        
        # BSSID, CLIENT, BSSID 
        if p.type == 0 and p.subtype == 0: # Association Request
            #print p.addr2, p.addr3
            CLIENT = p.addr2
            BSSID = p.addr1
        
        # CLIENT, BSSID, BSSID 
        if p.type == 0 and p.subtype == 1: # Association Response
            #print p.addr2, p.addr3
            CLIENT = p.addr1
            BSSID = p.addr2
        
        # CLIENT, FF: # + SSID (sometimes)
        if p.type == 0 and p.subtype == 4: # Probe Request
            #print p.addr2, p.addr3
            CLIENT = p.addr2
            try: SSID = str(p.info)
            except: SSID = ""
        
        # CLIENT, BSSID, BSSID # + SSID
        if p.type == 0 and p.subtype == 5: # Probe Response
            #print p.addr2, p.addr3
            CLIENT = p.addr1
            BSSID = p.addr3
            try: SSID = str(p.info)
            except: SSID = ""
        
        # 00:, BSSID, BSSID # + SSID
        if p.type == 0 and p.subtype == 8: # Beacon Frame
            #print p.addr2, p.addr3
            #CLIENT = p.addr2
            BSSID = p.addr2
            try: SSID = str(p.info)
            except: SSID = ""
        
        # BSSID, CLIENT, BSSID 
        if p.type == 0 and p.subtype == 10: # Disassociation
            #print p.addr2, p.addr3
            CLIENT = p.addr2
            BSSID = p.addr1
        
        # CLIENT, BSSID, BSSID 
        if p.type == 0 and p.subtype == 11: # Authentication
            #print p.addr2, p.addr3
            CLIENT = p.addr1
            BSSID = p.addr2
        
        # CLIENT, BSSID [re-check] (NOT IN BSSID?)
        if p.type == 0 and p.subtype == 13: # Acknowledgement | Action frames
            pass
            #print p.addr2, p.addr3
            #CLIENT = p.addr2
            #BSSID = p.addr1
        
        # TYPE = 1
        
        # BSSID, CLIENT, 00:
        if p.type == 1 and p.subtype == 10:
            #print p.addr2, p.addr3
            CLIENT = p.addr2
            BSSID = p.addr1
        
        # BSSID, CLIENT, 00: or CLIENT, BSSID, 00: [re-check] (NOT IN BSSID?)
        if p.type == 1 and p.subtype == 11: 
            pass
            #print p.addr2, p.addr3	
            #CLIENT = p.addr2
            #BSSID = p.addr1
        
        # CLIENT, 00:, 00: 
        if p.type == 1 and p.subtype == 12: 
            #print p.addr2, p.addr3	
            CLIENT = p.addr1
            #BSSID = p.addr1
        
        # TYPE = 2
        
        # CLIENT, BSSID, CLIENT
        if p.type == 2 and p.subtype == 0:
            #print p.addr2, p.addr3
            CLIENT = p.addr1 # p.addr1?
            BSSID = p.addr2
            
        # CLIENT, BSSID
        if p.type == 2 and p.subtype == 4:
            #print p.addr2, p.addr3
            CLIENT = p.addr2
            BSSID = p.addr3
        
        # BSSID, CLIENT, BSSID
        if p.type == 2 and p.subtype == 8: 
            #print p.addr2, p.addr3
            CLIENT = p.addr2
            BSSID = p.addr1
        
        # BSSID, CLIENT, BSSID
        if p.type == 2 and p.subtype == 12:
            #print p.addr2, p.addr3
            CLIENT = p.addr2
            BSSID = p.addr1
        
        if BSSID in EXCLUDE: BSSID = "" # CLEAN BSSID

        if BSSID not in INVENTORY and BSSID not in EXCLUDE and p.type == 0:
            INVENTORY[BSSID] = set()
            print "added..." + str(BSSID)
            print SSID
            if BSSID in aps:
                print aps[BSSID]
            else:
                print ">"+str(BSSID)+"??"
            print
        
        #return
        
        if BSSID in INVENTORY and BSSID not in EXCLUDE and p.type in TYPE and p.subtype in SUBTYPE: 
            
            if CLIENT != BSSID and CLIENT not in EXCLUDE: INVENTORY[BSSID].add(CLIENT)
            #return
            print
            #print "START..."
            
            check_delay_time = int(time.time()) - init_delay_time
            
            if LOG != "" and DELAY < check_delay_time:
                with open(LOG, 'w') as f:
                    f.write("")
            
            
            for i in INVENTORY:

                o_bssid = str(i)
                try: o_ssid = str(aps[i][0])
                except: o_ssid = ""
                try: o_channel = str(aps[i][1])
                except: o_channel = ""
                try: o_crypto = str("|".join(aps[i][2]))
                except: o_crypto = ""
                try: o_signal = str(aps[i][3])
                except: o_signal = ""
                o_client = str("|".join(INVENTORY[i]))
                
                OUT = o_bssid +","+ o_ssid +","+ o_channel +","+ o_crypto +","+ o_signal +","+ o_client
                
                print OUT
                
                if LOG != "" and DELAY < check_delay_time:
                    
                    with open(LOG, 'a') as f:
                        f.write(OUT + "\n")
            
            if DELAY < check_delay_time:
                init_delay_time = int(time.time())
                
        return
            
    except Exception as e:
        pass
        print "** Error: " + str(traceback.format_exc())
        print


# Channel hopper - This code is very similar to that found in airoscapy.py (http://www.thesprawl.org/projects/airoscapy/)
def channel_hopper(interface):
    global CHANNEL
    while True:
        try:
            #channel = random.randrange(1,13)
            channel = random.choice(CHANNEL)
            os.system("iwconfig %s channel %d" % (interface, channel))
            time.sleep(1)
        except KeyboardInterrupt:
            break

def stop_channel_hop(signal, frame):
    # set the stop_sniff variable to True to stop the sniffer
    global stop_sniff
    stop_sniff = True
    channel_hop.terminate()
    channel_hop.join()

try:
    channel_hop = Process(target = channel_hopper, args=(INTERFACE,))
    channel_hop.start()
    signal.signal(signal.SIGINT, stop_channel_hop)
    sniff(iface=INTERFACE, prn=sniffmgmt)
except Exception as e:
    print str(e)
    print "Bye ;)"
