<h1 class="text-xl mb-4">Přihlášení do bakalářů</h1>
<?php echo $warning; ?>
<p class="text-2xs mb-3">Nemáme nic společného s firmou BAKALÁŘI software.</p>
<form method="post" id="form-baka" >

    <hr class="bg-blue max-w-1/2 mx-auto  bg-blue border-0 h-px mb-8 md:mb-10">

    <div class="">
        <!--<p><label for="bakaUser">Vaše přihlašovací jméno </label> </p>-->
        <p><input type="text" id="bakaUser" name="bakaUser" required placeholder="přhlašovací jméno"
               class="text-center  p-2 bg-transparent border-blue rounded-full border-2 mb-4 min-w-30 focus:outline-none
               focus:border-orange  hover:border-orange focus:min-w-35 focus:transition ease-in-out duration-500" ></p>
        <!--<p><label for="bakaPass" > Vaše heslo do bakalářů </label> </p>-->
        <p><input type="password" id="bakaPass" name="bakaPass" placeholder="heslo" required
               class="text-center p-2 bg-transparent border-blue rounded-full border-2 min-w-30 focus:outline-none
               focus:border-orange  hover:border-orange focus:min-w-35 focus:transition ease-in-out duration-500" ></p>
    </div>

    <hr class="bg-blue max-w-1/2 mx-auto  bg-blue border-0 h-px my-8 md:my-10">
    <p><button type="submit" id="login" name="login"  class="p-2 bg-transparent border-orange rounded-full border-2 min-w-30 focus:outline-none
               focus:border-green focus:bg-green focus:text-black hover:border-green hover:min-w-35 focus:min-w-35 focus:transition ease-in-out duration-500" > Přihlásit se </button></p>
</form>