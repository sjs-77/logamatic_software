#!usr/bin/python3 -u
# -*-coding:Utf-8 -*
#
# Class logamatic2107
# ~~~~~~~~~~~~~~~~~~~
#
# Python 3 class for reading status data from a Buderus Logamatic 2107 control unit
#
# License: CC-BY-SA 3.0
# Author: Sebastian Suchanek
#
# When the constructor of this class is called, the connection to the serial port
# is established and new thread is initialised. After that, a data telegram
# "0xEE 0x00 0x00" is sent to the Logamatic 2107 unit, triggering a full status
# dump, of which all known values are transferred into a status database table.
# After the dump is received, the unit will continue to listen to the data sent from
# the Logamatic 2107 in regular intervals. "Keep alive" data telegrams are discarded.
# If known data telegrams are received, the status database table is updated
# accordingly. If an unknown data telegram is received, the full telegram is stored
# in a log database table for pontential future analysis.


from c3964r import Dust3964r
import threading
import pymysql

ende = False

class logamatic2107 (Dust3964r,threading.Thread):


    # Constructor
    def __init__ (self):
        # Initiate class for reading the 3964 data protocol.
        # Adjust name of the serial device in the following line if necessary.
        Dust3964r.__init__ (self,port='/dev/ttyAMA0',baudrate=2400)
        threading.Thread.__init__ (self)
        print("Starting initial query of Logamatic.")
        Dust3964r.newJob(self,b"\xEE\x00\x00")

    # Log any given telegram to database
    def LogToDB (self,telegram):
        # Connect to database - adjust credentials as needed.
        db = pymysql.connect("SERVER","USER","PASSWORD","DATABASE" )
        cursor = db.cursor()

        sql = "INSERT INTO rawlog (length, telegram_byte1, telegram_byte2, telegram_byte3, telegram_byte4, telegram_byte5, telegram_byte6, telegram_byte7, telegram_byte8, telegram_byte9, telegram_byte10) VALUES ("
        sql += str(len(telegram)) + ", "
        sql += str(telegram[0]) + ", " + str(telegram[1]) + ", " + str(telegram[2]) + ", "
        if len(telegram) == 3:
             sql += "NULL, NULL, NULL, NULL, NULL, NULL, NULL)"
        elif len(telegram) == 4:
             sql += str(telegram[3]) + ", NULL, NULL, NULL, NULL, NULL, NULL)"
        elif len(telegram) == 5:
             sql += str(telegram[3]) + ", " + str(telegram[4]) + ", NULL, NULL, NULL, NULL, NULL)"
        elif len(telegram) == 6:
             sql += str(telegram[3]) + ", " + str(telegram[4]) + ", " + str(telegram[5]) + ", NULL, NULL, NULL, NULL)"
        elif len(telegram) == 7:
             sql += str(telegram[3]) + ", " + str(telegram[4]) + ", " + str(telegram[5]) + ", " + str(telegram[6]) + ", NULL, NULL, NULL)"
        elif len(telegram) == 8:
             sql += str(telegram[3]) + ", " + str(telegram[4]) + ", " + str(telegram[5]) + ", " + str(telegram[6]) + ", " + str(telegram[7]) + ", NULL, NULL)"
        elif len(telegram) == 9:
             sql += str(telegram[3]) + ", " + str(telegram[4]) + ", " + str(telegram[5]) + ", " + str(telegram[6]) + ", " + str(telegram[7]) + ", " + str(telegram[8]) + ", NULL)"
        elif len(telegram) == 10:
             sql += str(telegram[3]) + ", " + str(telegram[4]) + ", " + str(telegram[5]) + ", " + str(telegram[6]) + ", " + str(telegram[7]) + ", " + str(telegram[8]) + ", " + str(telegram[9]) + ")"

        try:
             cursor.execute(sql)
             db.commit()
        except:
             db.rollback()

        db.close()

    # Write state to database
    def StateToDB (self,typeOfValue,value):
        # Connect to database - adjust credentials as needed.
        db = pymysql.connect("SERVER","USER","PASSWORD","DATABASE" )
        cursor = db.cursor()
        sql = "INSERT INTO current_state (id, " + typeOfValue + ") VALUES (1, " + str(value) + ") ON DUPLICATE KEY UPDATE " + typeOfValue + " = " + str(value)
        try:
             cursor.execute(sql)
             db.commit()
        except:
             db.rollback()
        db.close()


    # Main procedure for thread
    def run (self):
        global ende
        while not ende:
            self.running ()

    # Eventhandler that is called from the 3964 unit if a data telegram is received successfully
    def ReadSuccess (self,telegram):
        if len(telegram)==3:
            # Data telegram found
            ID= telegram [0:2]
            # Evaluate content of telegram
            if ID==b"\x80\x00":
                self.StateToDB("hc1_state_1",telegram[2])
            elif ID==b"\x80\x01":
                self.StateToDB("hc1_state_2",telegram[2])
            elif ID==b"\x80\x02":
                self.StateToDB("hc1_feedtemp_set",telegram[2])
            elif ID==b"\x80\x03":
                self.StateToDB("hc1_feedtemp_act",telegram[2])
            elif ID==b"\x80\x04":
                self.StateToDB("hc1_roomtemp_set",telegram[2])
            elif ID==b"\x80\x05":
                self.StateToDB("hc1_roomtemp_act",telegram[2])
            elif ID==b"\x80\x08":
                self.StateToDB("hc1_pump",telegram[2])
            elif ID==b"\x80\x0C":
                self.StateToDB("hc1_curve_p10",telegram[2])
            elif ID==b"\x80\x0D":
                self.StateToDB("hc1_curve_0",telegram[2])
            elif ID==b"\x80\x0E":
                self.StateToDB("hc1_curve_m10",telegram[2])

            elif ID==b"\x81\x1E":
                # Heating Circuit 2, characteristic curve +10°C
                return
            elif ID==b"\x81\x1F":
                # Heating Circuit 2, characteristic curve 0°C
                return
            elif ID==b"\x81\x20":
                # Heating Circuit 2, characteristic curve -10°C
                return

            elif ID==b"\x84\x24":
                self.StateToDB("ww_state_1",telegram[2])
            elif ID==b"\x84\x25":
                self.StateToDB("ww_state_2",telegram[2])
            elif ID==b"\x84\x26":
                self.StateToDB("ww_temp_set",telegram[2])
            elif ID==b"\x84\x27":
                self.StateToDB("ww_temp_act",telegram[2])
            elif ID==b"\x84\x29":
                self.StateToDB("ww_state_pumps",telegram[2])

            elif ID==b"\x88\x2a":
                self.StateToDB("boiler_temp_set",telegram[2])
            elif ID==b"\x88\x2b":
                self.StateToDB("boiler_temp_act",telegram[2])
            elif ID==b"\x88\x2c":
                self.StateToDB("boiler_burner_on",telegram[2])
            elif ID==b"\x88\x2d":
                self.StateToDB("boiler_burner_off",telegram[2])
            elif ID==b"\x88\x2e":
                # Unknown "Kesselintegral"
                return
            elif ID==b"\x88\x2f":
                # Unknown "Kesselintegral"
                return
            elif ID==b"\x88\x30":
                self.StateToDB("boiler_errors",telegram[2])
            elif ID==b"\x88\x31":
                self.StateToDB("boiler_state_1",telegram[2])
            elif ID==b"\x88\x32":
                self.StateToDB("boiler_burner_state_1",telegram[2])
            elif ID==b"\x88\x36":
                self.StateToDB("boiler_hours1_3",telegram[2])
            elif ID==b"\x88\x37":
                self.StateToDB("boiler_hours1_2",telegram[2])
            elif ID==b"\x88\x38":
                self.StateToDB("boiler_hours1_1",telegram[2])

            elif ID==b"\x89\x3c":
                if telegram[2]>127:
                    self.StateToDB("conf_amb_temp",(telegram[2]-256))
                else:
                    self.StateToDB("conf_amb_temp",telegram[2])
            elif ID==b"\x89\x3d":
                if telegram[2]>127:
                    self.StateToDB("conf_amb_temp_filtered",(telegram[2]-256))
                else:
                    self.StateToDB("conf_amb_temp_filtered",telegram[2])

            else:
                print("Unknown data telegram:", "%0#2.2x"% telegram[0], "%0#2.2x"% telegram[1], "%0#2.2x"% telegram[2],  sep=" ")
                self.LogToDB(telegram)

        elif len(telegram)==8:
            ID= telegram [0:8]
            if ID==b"\x04\x00\x07\x01\x81\x0E\xC0\x04":
                # Keep-alive telegram
                return
            else:
                print("Unknown data telegram:", "%0#2.2x"% telegram[0], "%0#2.2x"% telegram[1], "%0#2.2x"% telegram[2], "%0#2.2x"% telegram[3], "%0#2.2x"% telegram[4], "%0#2.2x"% telegram[5], "%0#2.2x"% telegram[6], "%0#2.2x"% telegram[7],  sep=" ")
                self.LogToDB(telegram)

        else:
            print("Unknown data telegram. Address:", "%0#2.2x"% telegram[0], "%0#2.2x"% telegram[1],  sep=" ")
            self.LogToDB(telegram)


