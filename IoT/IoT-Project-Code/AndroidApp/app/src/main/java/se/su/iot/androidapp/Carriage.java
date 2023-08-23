package se.su.iot.androidapp;

import java.util.Objects;

public class Carriage implements Comparable<Carriage> {

    private Train train;
    private final int id;
    private final int position;

    private double crowdedness;

    public Carriage(int id, int position) {
        this.id = id;
        this.position = position;
        this.crowdedness = 0;
    }

    public void setTrain(Train train) {
        this.train = train;
    }

    @Override
    public boolean equals(Object o) {
        if ( o instanceof Carriage ) {
            Carriage other = (Carriage) o;
            return this.id == other.id;
        }
        return false;
    }

    @Override
    public int hashCode() {
        return Objects.hash(id);
    }

    public Train getTrain() {
        return this.train;
    }

    public int getId() {
        return this.id;
    }

    public int getPosition() {
        return this.position;
    }

    public double getCrowdedness() {
        return this.crowdedness;
    }

    public void setCrowdedness(double crowdedness) {
        this.crowdedness = crowdedness;
    }


    @Override
    public int compareTo(Carriage other) {
        return this.position - other.position;
    }
}
