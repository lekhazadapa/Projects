package se.su.iot.androidapp;

import androidx.appcompat.app.AppCompatActivity;

import android.Manifest;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;

import org.altbeacon.beacon.Beacon;
import org.altbeacon.beacon.BeaconManager;
import org.altbeacon.beacon.Identifier;
import org.altbeacon.beacon.RangeNotifier;
import org.altbeacon.beacon.Region;
import org.altbeacon.beacon.service.ArmaRssiFilter;
import org.altbeacon.beacon.service.RangedBeacon;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.IOException;
import java.util.Collection;
import java.util.concurrent.ExecutionException;

import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.Response;

public class MainActivity extends AppCompatActivity {

    private BeaconManager beaconManager;
    private boolean switchingMode = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        getBluetoothPermissions();

        RangedBeacon.setSampleExpirationMilliseconds(3000);
        beaconManager = BeaconManager.getInstanceForApplication(this);
        beaconManager.setForegroundBetweenScanPeriod(0);
        beaconManager.setForegroundScanPeriod(100);
        beaconManager.setBackgroundBetweenScanPeriod(0);
        beaconManager.setBackgroundScanPeriod(100);
        beaconManager.startRangingBeacons(new Region("UserRegion", null, null, null));

    }

    @Override
    protected void onResume() {
        super.onResume();
        beaconManager.addRangeNotifier(getRangeNotifier());
        switchingMode = false;
    }

    @Override
    protected void onPause() {
        super.onPause();
        beaconManager.removeAllRangeNotifiers();
    }

    private void switchPlatformActivity(Platform platform) {
        if (switchingMode) {
            return;
        }
        switchingMode = true;
        Intent moveScreenIntent = new Intent(MainActivity.this, PlatformActivity.class);
        moveScreenIntent.putExtra("platform", platform);
        startActivity(moveScreenIntent);
    }


    private RangeNotifier getRangeNotifier() {
        return (Collection<Beacon> beacons, Region region) -> {

            for (Beacon beacon : beacons) {

                Identifier identifier = beacon.getIdentifier(0);

                JSONObject json = sendPlatformRequest(identifier.toString());
                try {

                    String name = json.getJSONObject("platform").getString("name");
                    double length = json.getJSONObject("platform").getDouble("length");
                    JSONArray sensors = json.getJSONArray("sensors");

                    Platform platform = new Platform(name, length);
                    for (int i = 0; i < sensors.length(); i++) {
                        JSONObject instance = sensors.getJSONObject(i);
                        Sensor sensor = new Sensor(instance.getString("uuid"), instance.getDouble("position"), instance.getDouble("height"));
                        platform.addSensor(sensor);
                    }

                    switchPlatformActivity(platform);
                    break;

                } catch (JSONException e) {
                    e.printStackTrace();
                }

            }

        };
    }

    private void getBluetoothPermissions() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
            requestPermissions(new String[]{Manifest.permission.ACCESS_FINE_LOCATION, Manifest.permission.ACCESS_BACKGROUND_LOCATION}, 1);
            requestPermissions(new String[]{Manifest.permission.ACCESS_BACKGROUND_LOCATION}, 2);
        }
        else {
            requestPermissions(new String[]{Manifest.permission.ACCESS_COARSE_LOCATION}, 1);
        }
    }

    private static class GetPlatformTask extends AsyncTask<String, Void, JSONObject> {

        @Override
        protected JSONObject doInBackground(String... params) {

            OkHttpClient client = new OkHttpClient();

            Request request = new Request.Builder().url("http://iot.studentenfix.se/sensor/platform/" + params[0] + "/").build();

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

    private JSONObject sendPlatformRequest(String uri) {

        JSONObject json = null;

        try {
            json = new GetPlatformTask().execute(uri).get();
        } catch (ExecutionException | InterruptedException e) {
            e.printStackTrace();
        }

        return json;

    }

}