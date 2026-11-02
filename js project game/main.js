        //selecting input box,paragraph
        var guessnumber = document.getElementById("guessnumber")
        var result = document.getElementById("result")
        var score = document.getElementById("score")
        var randomNumber = Math.floor(Math.random()*5)+1
        var totalscore=10

        function check()
        {
            var enterednumber = guessnumber.value
            if(randomNumber==enterednumber)
            { 
                console.log("Right")
                result.textContent="Right"
                alert("YOU WON...")
            }

            else
            {
                totalscore=totalscore-1
                score.textContent="score:"+totalscore
                result.textContent="Wrong"
            }
        
        


        }
        