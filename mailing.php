<?php
//Küldő mail cím
$noreplyAddress = "noreply@wewrite.hu";

$name = $chosen = $email = "";
if (!empty($_POST["name"])) {
    $name = $_POST["name"];
}
if (!empty($_POST["email"])) {
    $email = $_POST["email"];
}
if (!empty($_POST["chosen"])) {
    $chosen = $_POST["chosen"];
}
if ($name != "") {
    try {
        $message = 'Szia '.$name.'!<br><br>Az általad húzott személy: '.$chosen.'.<br><br>Kellemes karácsonyi készülődést kívánunk!';
        $header = "From: ".$noreplyAddress."\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html\r\n";
        //endregion
        mail($email,"Karácsonyi húzás", $message, $header);
        echo true;
    } catch (Exception $e) {
        echo false;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Húzás</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        html, body {
            height: 100%;
        }

        body {
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
        }

        .form-signin {
            width: 100%;
            max-width: 330px;
            padding: 15px;
            margin: auto;
        }

        .form-signin .form-floating:focus-within {
            z-index: 2;
        }
    </style>
</head>
<body>
<main class="form-signin">
    <form>
        <h1 class="h3 mb-3 fw-normal">Résztvevők importálása</h1>
        <div class="mb-3">
            <label for="formFile" class="form-label">Táblázat (*.csv)</label>
            <input class="form-control" type="file" id="formFile">
        </div>
        <button class="w-100 btn btn-lg btn-primary" type="button" onclick="process(true)">Importálás</button>
        <button class="w-100 btn btn-lg btn-outline-primary mt-2" type="button" onclick="process(false)">Teszt</button>
        <p class="mt-5 mb-3 text-muted">© 2021</p>
    </form>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script>
    let allUser = [];
    document.getElementById('formFile').addEventListener('change', function() {
        let fr = new FileReader();
        fr.onload = function() {
            try {
                let invalidRows = []
                fr.result.split("\r\n").forEach((row, rowIndex) => {
                    let currentRow = row.split(";")
                    if (currentRow.length !== 3) {
                        invalidRows.push(rowIndex)
                    } else {
                        if (currentRow[0] !== '') {
                            allUser.push({
                                name: currentRow[0],
                                type: currentRow[1],
                                mail: currentRow[2],
                            })
                        }
                    }
                })
                if (invalidRows.length > 0) {
                    throw 'Nem megfelelő az oszlopok száma a(z) ' + invalidRows.join(', ') + ((invalidRows.length > 1) ? 'sorokban' : 'sorban')
                }
                console.log('Imported users: ', allUser)
            } catch (e) {
                console.warn(e)
                console.log(allUser)
            }
        }
        fr.readAsText(this.files[0], "latin2");
    })

    function process2() {
        try {
            let chosen = []
            let finalResult = []
            let all = [...allUser]
            while (all.length > 0) {
                console.log(chosen)
                let currentIndex = randomIntFromInterval(1, all.length) -1;
                console.log("Max:",all.length, "Got:", currentIndex)
                let currentUser = all[currentIndex]
                if (currentUser.name !== '') {
                    console.log("Húzó:", currentUser.name)
                    let fb = [...allUser];
                    let filteredUsers = fb.filter((givenUser) => {
                        return !(givenUser.type === currentUser.type || chosen.includes(givenUser.name))
                    })
                    console.log("Elérhetők:")
                    console.log(filteredUsers)
                    let index = randomIntFromInterval(1, filteredUsers.length) -1;
                    console.log("Flitered max:", filteredUsers.length, "Filtered got", index)
                    chosen.push(filteredUsers[index].name)
                    finalResult.push({
                        from: currentUser,
                        to: filteredUsers[index]
                    })
                    all.splice(currentIndex, 1)
                    console.log("Húzott:", filteredUsers[index].name)
                }
            }
            finalResult.forEach((choice) => {
                console.log(choice.from.name + ' > ' + choice.to.name)
                /*$.post(
                    'index.php',
                    {
                        name:   choice.from.name,
                        email:  choice.from.mail,
                        chosen: choice.to.name
                    },
                    function (result) {
                        console.log('Kiküldve ide: '+choice.from.mail+" - "+result)
                    })*/
            })
        } catch (e) {
            console.warn(e)
        }
    }

    function isValid(finalResult) {
        console.log("----------------------")
        let original = [...finalResult]
        if (allUser.length !== original.length)
            return false;
        finalResult.forEach((choice) => {
            let huzo = choice.from.name
            let huzott = choice.to.name
            let cross = original.filter(c => c.to.name === huzo && c.from.name === huzott)
            if (cross.length > 0) {
                return false;
            }
        })
        console.log("----------------------")
        return true
    }

    function process(prod) {
        try {
            let finalResult = []
            let resztvevok = [...allUser]
            let kalap = [...allUser]
            while (resztvevok.length > 0) {
                let rndHuzo = randomIntFromInterval(0, resztvevok.length-1)
                let resztvevoMostani = resztvevok[rndHuzo]
                resztvevok.splice(rndHuzo, 1)
                let modKalap = [...kalap].filter((cetli) => {
                    return cetli.type !== resztvevoMostani.type
                })
                let rndHuzott = randomIntFromInterval(0, modKalap.length-1)
                let huzott = modKalap[rndHuzott]
                kalap.splice(kalap.indexOf(huzott), 1)
                console.log(resztvevoMostani, huzott, kalap)

                if (huzott === undefined)
                    throw 'Try again!'

                finalResult.push({
                    from: resztvevoMostani,
                    to: huzott
                })
            }
            if (!isValid(finalResult))
                process(prod)
            finalResult.forEach((choice) => {
                console.log(choice.from.name + ' > ' + choice.to.name)
                if (prod)
                    $.post(
                        '/',
                        {
                            name:   choice.from.name,
                            email:  choice.from.mail,
                            chosen: choice.to.name
                        },
                        function (result) {
                            console.log('Kiküldve ide: '+choice.from.mail+" - "+result)
                        })
            })
        } catch (e) {
            console.warn(e)
            process(prod)
        }
    }

    function randomIntFromInterval(min, max) { // min and max included
        return Math.floor(Math.random() * (max - min + 1) + min)
    }
</script>
</body>
</html>
