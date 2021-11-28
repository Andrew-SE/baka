

<form method="post">
    <h1 style="color:#a7d5f0; margin-bottom: 40px;">BAKATEAM</h1>
    <div class="left">
        <p><button type="submit" name="calendarActual" >Nahrát aktuální rozvrh</button></p>
        <p><button type="submit" name="calendarNextWeek">Rozvrh na příští týden</button></p>
        <p><button type="submit" name="calendarPermanent" >Stálý rozvrh na měsíc</button></p>

    </div>
    <div class="right">
        <p><button type="submit" name="delete_next">Smazat příští týden</button></p>
        <p><button type="submit" name="deletePermanent" >SMAZAT stálý rozvrh</button></p>
        <p><button type="submit" name="delete" >SMAZAT aktuální rozvrh</button></p>
    </div>

    <h3>Upozornění</h3>
    <p><label for="reminder">Vypnout</label>
        <input type="checkbox" id="reminder" name="reminder" value="true"></p>
    <p><label for="timer">Čas (min)</label>
        <input type="number" id="timer" name="timer" min="1" max="20" value="5"></p>

</form>