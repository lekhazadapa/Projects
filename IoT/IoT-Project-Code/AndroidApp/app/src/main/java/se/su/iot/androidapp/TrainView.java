package se.su.iot.androidapp;

import android.content.Context;
import android.graphics.Canvas;
import android.graphics.Paint;
import android.graphics.Rect;
import android.util.AttributeSet;
import android.view.View;

import java.util.List;
import java.util.SortedSet;

public class TrainView extends View {

    private int width;
    private int height;
    private int paddingX;
    private int paddingY;

    private Paint carriagePaint;
    private Rect carriageRect;

    private Train train;

    public TrainView(Context c) {
        this(c, null);
    }

    public TrainView(Context context, AttributeSet attrs) {
        super(context, attrs);
        init();
    }

    public void setTrain(Train train) {
        this.train = train;
    }

    public Train getTrain() {
        return this.train;
    }

    private void init() {
        carriagePaint = new Paint();
        carriageRect = new Rect();
    }

    @Override
    protected void onDraw(Canvas canvas) {
        super.onDraw(canvas);

        width = this.getWidth();
        height = this.getHeight();

        paddingX = (int) (width * 0.25);
        paddingY = (int) (height * 0.1);

        int trainHeight = height - paddingY*2;
        int carriagePadding = (int) (trainHeight * 0.05);

        List<Carriage> carriages = train.getCarriages();
        int numberOfPaddings = carriages.size() - 1;
        int carriageHeight = trainHeight / carriages.size();
        int crowdPadding = (int) (carriageHeight * 0.05);

        for ( Carriage carriage : carriages ) {

            carriagePaint.setARGB(255, 202, 207, 210);
            carriageRect.set(
                    paddingX,
                    paddingY + (carriage.getPosition() - 1) * carriageHeight + carriagePadding/2,
                    width-paddingX,
                    paddingY + carriage.getPosition() * carriageHeight - carriagePadding/2
            );
            canvas.drawRect(carriageRect, carriagePaint);

            if ( carriage.getCrowdedness() <= 0.33 ) {
                carriagePaint.setARGB(255, 46, 204, 113);
            }
            else if ( carriage.getCrowdedness() <= 0.67 ) {
                carriagePaint.setARGB(255, 243, 156, 18);
            }
            else {
                carriagePaint.setARGB(255, 231, 76, 60);
            }
            carriageRect.set(
                    paddingX + crowdPadding,
                    paddingY + (carriage.getPosition() - 1) * carriageHeight + carriagePadding/2 + crowdPadding,
                    width-paddingX-crowdPadding,
                    paddingY + carriage.getPosition() * carriageHeight - carriagePadding/2 - crowdPadding
            );
            canvas.drawRect(carriageRect, carriagePaint);

        }

    }


}
