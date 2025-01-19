function activateKepicCode() {
    document.getElementById('fetchDataBtn').remove();
  
    const input = document.createElement('input');
    input.type = 'text';
    input.id = 'dataInput';
  
    const submitBtn = document.createElement('button');
    submitBtn.innerHTML = 'Submit';
    submitBtn.id = 'submitBtn'; // Set the ID for the submit button
    submitBtn.onclick = checkCode;

    document.getElementById('inputContainer').appendChild(input);
    document.getElementById('inputContainer').appendChild(submitBtn);
  }
  
  function checkCode() {
    // Get the value from the input
    const inputValue = document.getElementById('dataInput').value;
  
    // Send the value to the PHP function using AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'kepiccodes.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
        // Get the response from the PHP function
        const response = xhr.responseText;
  
        // Remove the input and submit button
        document.getElementById('dataInput').remove();
        document.getElementById('submitBtn').remove();
        // Create a button with the second column's value as href
        if(response==="") {
        const responseText = document.createElement('p');
        responseText.innerHTML = "Your kepic code is not valid";
        document.getElementById('originalDiv').appendChild(responseText);
        return;
        }
        const responseText = document.createElement('a');
        responseText.href = "kepic/"+response;
        responseText.innerHTML = "Unlocked: " + response + " (click to download)";
        document.getElementById('originalDiv').appendChild(responseText);
      }
    };
  
    // Send the request with the input value
    xhr.send('inputValue=' + inputValue);
  }
  