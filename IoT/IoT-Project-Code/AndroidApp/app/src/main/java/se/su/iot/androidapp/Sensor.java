package se.su.iot.androidapp;

import java.io.Serializable;

public class Sensor implements Serializable {

    private final String uuid;
    private final double position;
    private final double height;
    private double distance;

    public Sensor(String uuid, double position, double height) {
        this.uuid = uuid.toLowerCase();
        this.position = position;
        this.height = height;
        this.distance = Double.MAX_VALUE;
    }

    public String getUuid() {
        return uuid;
    }

    public double getPosition() {
        return position;
    }

    @Override
    public boolean equals(Object other) {
        if ( other instanceof Sensor) {
            Sensor sensor = (Sensor) other;
            return this.uuid.equalsIgnoreCase(sensor.uuid);
        }
        return false;
    }

    @Override
    public int hashCode() {
        return uuid.hashCode();
    }

    @Deprecated
    public void setDistance(double distance) {
        this.distance = distance;
    }

    public void calculateDistance(double distance) {
        double calculatedDistance = Math.sqrt( Math.pow(distance, 2) - Math.pow(height, 2) );
        if ( Double.isNaN(calculatedDistance) ) {
            this.distance = 0.0;
        }
        else {
            this.distance = calculatedDistance;
        }
    }

    public double getDistance() {
        return distance;
    }

    public double getHeight() {
        return height;
    }

}
