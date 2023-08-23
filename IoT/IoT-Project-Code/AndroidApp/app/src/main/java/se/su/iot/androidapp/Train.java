package se.su.iot.androidapp;

import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.SortedSet;
import java.util.TreeSet;

public class Train {

    private final int id;

    private final List<Carriage> carriages;

    public Train(int id) {
        this.id = id;
        this.carriages = new ArrayList<>();
    }

    public int getId() {
        return this.id;
    }

    public void addCarriage(Carriage carriage) {
        if ( carriages.contains(carriage) ) {
            return;
        }
        carriages.add(carriage);
        carriages.sort(Carriage::compareTo);
    }

    public void updateCrowdedness(int index, double crowdedness) {
        carriages.get(index).setCrowdedness(crowdedness);
    }

    public void removeCarriage(Carriage carriage) {
        carriages.remove(carriage);
        carriages.sort(Carriage::compareTo);
    }

    public boolean containsCarriage(Carriage carriage) {
        return carriages.contains(carriage);
    }

    public List<Carriage> getCarriages() {
        return Collections.unmodifiableList(carriages);
    }

}
