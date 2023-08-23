import time
import datetime
import json
import requests
import paho.mqtt.client as mqtt
import paho.mqtt.publish as publish
import board
import adafruit_vcnl4010
import adafruit_tca9548a

broker = "83.226.147.68"
#broker = "tcp://83.226.147.68:1883"

#Publish format (JSON string)
#{
#   id: '{id}',
#   availableSeats: '{number}',
#   occupiedSeats: '{number}'
#}

