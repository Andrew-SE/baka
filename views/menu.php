
<h1 class="text-xl mb-4" >Menu Rozvrhu</h1>
<hr class="bg-blue max-w-1/2 mx-auto  bg-blue border-0 h-px mb-3 md:mb-5">
<form method="post" class="">
    <div class="flex flex-col sm:flex-row  justify-center">
    <div class="p-3">
        <h2 class="text-lg font-semibold">Nahrát</h2>
        <p><button type="submit" name="calendarActual" class="p-3 my-3 bg-transparent border-green rounded-full border-2 min-w-1/2 sm:min-w-full focus:outline-none
               focus:border-green hover:text-black hover:bg-green transition ease-in-out duration-500">Aktuální rozvrh</button></p>
        <p><button type="submit" name="calendarNextWeek" class="p-3 my-3  bg-transparent border-green rounded-full border-2 min-w-1/2 sm:min-w-full focus:outline-none
               focus:border-green hover:text-black hover:bg-green transition ease-in-out duration-500">Rozvrh na příští týden</button></p>
        <p><button type="submit" name="calendarPermanent" class="p-3 my-3  bg-transparent border-green rounded-full border-2 min-w-1/2 sm:min-w-full focus:outline-none
               focus:border-green hover:text-black hover:bg-green transition ease-in-out duration-500">Stálý rozvrh na měsíc</button></p>

    </div>
    <div class="p-3">
        <h2 class="text-lg font-semibold">Smazat</h2>
        <p><button type="submit" name="delete" class="p-3 my-3  bg-transparent border-orange rounded-full border-2 min-w-1/2 sm:min-w-full focus:outline-none
               focus:border-red-600 focus:bg-red-500 hover:text-white hover:bg-orange transition ease-in-out duration-500">Smazat aktuální rozvrh</button></p>
        <p><button type="submit" name="delete_next" class="p-3 my-3  bg-transparent border-orange rounded-full border-2 min-w-1/2 sm:min-w-full focus:outline-none
               focus:border-red-600 focus:bg-red-500 hover:text-white hover:bg-orange transition ease-in-out duration-500">Smazat příští týden</button></p>
        <p><button type="submit" name="deletePermanent" class="p-3 my-3  bg-transparent border-orange rounded-full border-2 min-w-1/2 sm:min-w-full focus:outline-none
               focus:border-red-600 focus:bg-red-500 hover:text-white hover:bg-orange transition ease-in-out duration-500">Smazat stálý rozvrh</button></p>
    </div>
    </div>
    <h3 class="text-lg my-4">Upozornění</h3>
    <hr class="bg-blue max-w-1/4 mx-auto  bg-blue border-0 h-px mb-4 md:mb-6">
    <div class="">
        <p><label for="reminder" >Vypnout</label>
            <input type="checkbox" id="reminder" name="reminder" value="true" class="mb-4"></p>
        <p><label for="timer">Čas (min)</label>
            <input type="number" id="timer" name="timer" min="1" max="20" value="5" class="max-w-smr p-1.5 mb-1.5 text-center bg-transparent border-white border-2 rounded-full focus:border-blue focus:outline-none transition ease-in-out duration-500"></p>
    </div>
</form>