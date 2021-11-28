
<?php echo $warning; ?>
<form method="post" id="form-baka">
    <h1>Bakaláři formulář</h1>
    <hr>
    <p><label for="bakaUser">Vaše přihlašovací jméno </label> </p>
    <p><input type="text" id="bakaUser" name="bakaUser" required></p>
    <p><label for="bakaPass" > Vaše heslo do bakalářů </label> </p>
    <p><input type="password" id="bakaPass" name="bakaPass" required></p>
    <hr style="margin-top: 5%;">
    <p><button type="submit" id="login" name="login" > Přihlásit se </button></p>
</form>