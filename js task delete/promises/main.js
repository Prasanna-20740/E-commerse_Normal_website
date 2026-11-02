function checkOrder() {
    return new Promise((resolve, reject) => {
        let orderID = document.querySelector("#order-id").value;
        if (orderID === 'order123') {
            resolve("Your order is delivered.");
        } else {
            reject("Your order is pending.");
        }
    });
}

document.getElementById('checkBtn').addEventListener('click', () => {
    checkOrder()
        .then((success) => {
            document.querySelector('#result').innerHTML = `<span style="color: green;">${success}</span>`;
        })
        .catch((error) => {
            document.querySelector('#result').innerHTML = `<span style="color: red;">${error}</span>`;
        });
});
