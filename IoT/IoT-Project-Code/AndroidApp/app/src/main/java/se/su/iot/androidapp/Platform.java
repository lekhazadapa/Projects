package se.su.iot.androidapp;

import java.io.Serializable;
import java.util.Collections;
import java.util.HashSet;
import java.util.*;

public class Platform implements Serializable {

    private final String name;
    private final double length;
    private final Set<Sensor> sensors;

    public Platform(String name, double length) {
        this(name, length, new HashSet<>());
    }
    public Platform(String name, double length, Set<Sensor> sensors) {
        this.name = name;
        this.length = length;
        this.sensors = sensors;
    }

    public void addSensor(Sensor sensor) {
        sensors.add(sensor);
    }

    public String getName() {
        return name;
    }

    public double getLength() {
        return length;
    }

    public Set<Sensor> getSensors() {
        return Collections.unmodifiableSet(sensors);
    }

}
