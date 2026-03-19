<?php
use App\Models\User;

/*
 * Server Script, um Admins zu erstellen. Als frist Callback
 */
echo "Was möchten Sie tun?";
echo "<br>";
echo "Root Admin erstellen 1), Admin zurücksetzen 2)";
echo "<br>";
$input = fgets(STDIN);
$input = trim($input);

if($input == "1"){
    setDefaultAdmin();
}

function setDefaultAdmin()
{
    $name = "Admin";
    $pw = "ihespdssi";
    $email = "info@schwarz-und-web.de";

    echo "Individuelle Eingaben? 2)";
    $input = fgets(STDIN);

    if($input == "2"){
        echo "Name";
        $name =  trim(fgets(STDIN));
        echo "<br>";
        echo "Email";
        $email = trim(fgets(STDIN));
        echo "<br>";
        echo "Password";
        $pw = trim(fgets(STDIN));
        echo "<br>";

        echo $name;
        echo "<br>";
        echo "<br>";
        echo $pw;
        echo "<br>";
        echo "<br>";
        echo $email;

    }



    try{
        User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($pw),
             'role' => 1
        ]);
        echo "Admin set successfully";
    }catch(\Exception $e){
        echo $e->getMessage();
    }


}

function resetDefaultAdmin(){
    try{
        User::query()->where("role", 1)->delete();
         echo "Admin reset successfully";

    }catch(\Exception $e){
        echo $e->getMessage();
    }

}







