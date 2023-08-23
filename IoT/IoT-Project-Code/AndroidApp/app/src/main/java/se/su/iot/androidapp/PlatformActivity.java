package se.su.iot.androidapp;

import androidx.appcompat.app.AppCompatActivity;

import android.os.AsyncTask;
import android.os.Bundle;
import android.widget.TextView;

import org.altbeacon.beacon.Beacon;
import org.altbeacon.beacon.BeaconManager;
import org.altbeacon.beacon.Identifier;
import org.altbeacon.beacon.RangeNotifier;
import org.altbeacon.beacon.Region;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.eclipse.paho.android.service.MqttAndroidClient;
import org.eclipse.paho.client.mqttv3.*;

import java.io.IOException;
import java.util.Collection;
import java.util.UUID;
import java.util.concurrent.ExecutionException;

import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.Response;

public class PlatformActivity extends AppCompatActivity {

    private BeaconManager beaconManager;
    private Platform platform;
    private Location platformLocation;

    private TextView topText;

    private PlatformView platformView;
    private TrainView trainView;

    private MqttAndroidClient mqttClient;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_platform);

        platform = (Platform) getIntent().getSerializableExtra("platform");
        platformLocation = new Location(platform);

        topText = findViewById(R.id.topText);
        topText.setText(platform.getName());

        platformView = findViewById(R.id.platformView);
        trainView = findViewById(R.id.trainView);

        loadNextTrain();

        beaconManager = BeaconManager.getInstanceForApplication(this);

        connectMqtt();

    }

    private void connectMqtt() {

        MqttConnectOptions mqttOptions = new MqttConnectOptions();

        mqttClient = new MqttAndroidClient(getApplicationContext(), "tcp://83.226.147.68:1883", UUID.randomUUID().toString());

        try {
            mqttClient.connect(mqttOptions, null, new IMqttActionListener() {
                @Override
                public void onSuccess(IMqttToken asyncActionToken) {
                    subscribeTopic("train/"+trainView.getTrain().getId());
                }

                @Override
                public void onFailure(IMqttToken asyncActionToken, Throwable exception) {
                    System.out.println("Connection Failure");
                }
            });
        } catch (MqttException e) {
            e.printStackTrace();
        }

    }

    private void subscribeTopic(String topic) {
        try {
            mqttClient.subscribe(topic, 0, null, new IMqttActionListener() {
                @Override
                public void onSuccess(IMqttToken asyncActionToken) {
                    mqttClient.setCallback(getMqttCallback());
                }

                @Override
                public void onFailure(IMqttToken asyncActionToken, Throwable exception) {
                    System.out.println("Subscription Failure");
                }
            });
        } catch (MqttException e) {
            e.printStackTrace();
        }
    }

    private MqttCallback getMqttCallback() {
        return new MqttCallback() {
            @Override
            public void connectionLost(Throwable cause) {
                System.out.println("Connection Lost");
            }

            @Override
            public void messageArrived(String topic, MqttMessage message) throws Exception {
                String json = message.toString();
                JSONObject root = new JSONObject(json);

                JSONArray carriages = root.getJSONArray("carriages");
                for (int i = 0; i < carriages.length(); i++) {
                    JSONObject instance = (JSONObject) carriages.get(i);
                    int index = instance.getInt("position")-1;
                    double crowdedness = instance.getDouble("crowdedness");
                    trainView.getTrain().updateCrowdedness(index, crowdedness);
                }

                trainView.invalidate();

            }

            @Override
            public void deliveryComplete(IMqttDeliveryToken token) {

            }
        };
    }

    private JSONObject getNextTrain() {

        JSONObject json = null;

        try {
            json = new GetNextTrainTask().execute(platform.getName()).get();
        } catch (ExecutionException | InterruptedException e) {
            e.printStackTrace();
        }

        return json;

    }

    private void loadNextTrain() {
        JSONObject nextTrain = getNextTrain();

        try {

            JSONObject trainJson = nextTrain.getJSONObject("train");
            JSONArray carriagesJson = nextTrain.getJSONArray("carriages");

            Train train = new Train(trainJson.getInt("id"));

            for ( int i = 0; i < carriagesJson.length(); i++ ) {
                JSONObject instance = carriagesJson.getJSONObject(i);

                Carriage carriage = new Carriage(instance.getInt("id"), instance.getInt("position"));
                train.addCarriage(carriage);

            }

            trainView.setTrain(train);

        } catch (JSONException e) {
            e.printStackTrace();
        }
    }

    @Override
    protected void onResume() {
        super.onResume();
        beaconManager.addRangeNotifier(getRangeNotifier());
    }

    @Override
    protected void onPause() {
        super.onPause();
        beaconManager.removeAllRangeNotifiers();
    }


    private RangeNotifier getRangeNotifier() {
        return (Collection<Beacon> beacons, Region region) -> {

            for (Beacon beacon : beacons) {

                double distance = beacon.getDistance();
                Identifier identifier = beacon.getIdentifier(0);

                platformLocation.updateDistances(identifier.toString(), distance);

            }

            double position = platformLocation.getPositioning();
            double length = platform.getLength();

            //System.out.println("Position: " + position);

            platformView.locationChanged(position/length);

        };
    }


    private static class GetNextTrainTask extends AsyncTask<String, Void, JSONObject> {

        @Override
        protected JSONObject doInBackground(String... params) {

            OkHttpClient client = new OkHttpClient();

            Request request = new Request.Builder().url("http://iot.studentenfix.se/nextTrain/" + params[0] + "/").build();

            JSONObject json = null;

            try {
                Response response = client.newCall(request).execute();
                String result = response.body().string();

                json = new JSONObject(result);

            } catch (IOException | JSONException e) {
                e.printStackTrace();
            }

            return json;
        }
    }

}