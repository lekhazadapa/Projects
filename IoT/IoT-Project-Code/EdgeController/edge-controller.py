import time
import json
import requests
import paho.mqtt.client as mqtt

broker = "83.226.147.68"

carriageData = requests.get(url="http://iot.studentenfix.se/carriage/").json()

print(json.dumps(carriageData, indent=2))

trains = dict()
carriages = dict()

for carriage in carriageData:
    print(carriage)
    
    trainId = carriage['train_id']
    if trainId not in trains:
        trains[trainId] = {
            'id': trainId,
            'carriages': []
        }
    
    trains[trainId]['carriages'].append(carriage['id'])

    carriageId = carriage['id']
    carriages[carriageId] = {
        'id': carriageId,
        'position': carriage['position'],
        'train_id': trainId,
        'crowdedness': 0.0
    }

def getTrain(id):
    train = trains[id].copy()
    carriagesId = train['carriages']

    train['carriages'] = []

    for i in carriagesId:
        train['carriages'].append(carriages[i])

    return train

def updateCarriage(id, crowdedness):
    carriages[int(id)]['crowdedness'] = crowdedness


#creating a mqtt client instance
client = mqtt.Client()

#connecting to the mqtt broker
client.connect(broker)
client.loop_start()

def on_connect(client, uderdata, flags, rc):
    print("Connected")
    
client.on_connect = on_connect

def on_message(client, userdata, message):
    data = str(message.payload.decode("utf-8"))
    json_object= json.loads(data)
    print("Got "+message.topic+": " + str(json_object))
    carriageId = json_object['id']
    crowdedness = json_object['occupiedSeats'] / json_object['totalSeats']
    updateCarriage(carriageId, crowdedness)

client.on_message = on_message

#subscribing to the topic 
for id in carriages:
    print("Subscribing to: " + "carriage/"+str(id))
    client.subscribe("carriage/"+str(id), qos = 0)

#publsihing the information
while True:
    #Converting to json
    for id in trains:
        data = getTrain(id)
        json_data = json.dumps(data)
        print("Publish train/"+str(id)+": " + str(data))
        client.publish('train/'+str(id), json_data, qos = 0)
    time.sleep(3)