package se.su.iot.androidapp;

import java.util.*;

public class Location {

    private final Platform platform;
    private final Map<String, Sensor> sensorsByUuid;
    private final List<Sensor> sensorsInOrder;

    private double relativePosition;

    public Location(Platform platform) {
        this.platform = platform;

        this.sensorsByUuid = new HashMap<>();
        this.sensorsInOrder = new ArrayList<>(platform.getSensors().size());

        for ( Sensor sensor : platform.getSensors() ) {
            sensorsByUuid.put(sensor.getUuid(), sensor);
            sensorsInOrder.add(sensor);
        }
        sensorsInOrder.sort((first, second) -> {
            return (int) (first.getPosition()*100 - second.getPosition()*100);
        });

    }

    public double getPositioning() {
        double platformLength = platform.getLength();

        // Find the closest sensor
        Sensor closest = sensorsInOrder.get(0);
        int closestIndex = 0;
        for ( int i = 0; i < sensorsInOrder.size(); i++ ) {
            Sensor sensor = sensorsInOrder.get(i);
            //System.out.println(sensor.getUuid() + " " + sensor.getDistance());
            if (closest.getDistance() > sensor.getDistance()) {
                closest = sensor;
                closestIndex = i;
            }
        }

        // Get possible positions
        double sensorPosition = platformLength * closest.getPosition();

        double possiblePositionBefore = sensorPosition - closest.getDistance();
        double possiblePositionAfter = sensorPosition + closest.getDistance();

        // Get sensor situation
        Sensor before = null;
        Sensor after = null;
        double distanceToSensorBefore = Double.MAX_VALUE;
        double distanceToSensorAfter = Double.MAX_VALUE;

        if ( closestIndex != 0 ) {
            before = sensorsInOrder.get(closestIndex-1);
            distanceToSensorBefore = before.getDistance();
        }
        if ( closestIndex != (sensorsInOrder.size()-1) ) {
            after = sensorsInOrder.get(closestIndex+1);
            distanceToSensorAfter = after.getDistance();
        }

        // Decide on positioning
        double finalPosition = 0.0;
        if ( distanceToSensorBefore < distanceBetweenSensors(before, closest) ) {
            finalPosition = possiblePositionBefore;
        }
        else if ( distanceToSensorAfter < distanceBetweenSensors(closest, after) ) {
            finalPosition = possiblePositionAfter;
        }
        else if ( before == null && after != null ) {
            finalPosition = possiblePositionBefore;
        }
        else if ( after == null && before != null ) {
            finalPosition = possiblePositionAfter;
        }
        else {
            finalPosition = sensorPosition;
        }

        if ( finalPosition < 0 ) {
            return 0;
        }
        else if ( finalPosition > platformLength ) {
            return platformLength;
        }
        else {
            return finalPosition;
        }

    }

    private double distanceBetweenSensors(Sensor first, Sensor second) {
        if ( first == null || second == null ) {
            return Double.MAX_VALUE;
        }
        double firstPosition = platform.getLength() * first.getPosition();
        double secondPosition = platform.getLength() * second.getPosition();
        return Math.abs(firstPosition - secondPosition);
    }

    public void updateDistances(String uuid, double distance) {
        Sensor sensor = sensorsByUuid.get(uuid);
        if ( sensor == null ) {
            return;
        }
        sensor.calculateDistance(distance);
    }

}
