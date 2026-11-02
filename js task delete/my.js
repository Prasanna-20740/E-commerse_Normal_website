let tasks = JSON.parse(localStorage.getItem('tasks')) || [];
let resultElement = document.querySelector('#result');

function renderTasks(array){
     // reset result element
    resultElement.innerHTML ='';
     // append all values in result
     array.forEach((item) =>{
        let divElement = document.createElement('div');

        divElement.innerHTML = 
                                    `<h1>
                                    <input type="checkbox" ${item.isCompleted ? "checked" : ""}>
                                    ${item.name} - ${item.isCompleted ? "Completed" : ""}
                                    </h1>
                                    <button>Delete</button>
                                     `;

        //Add event listeners dynamically                           
        divElement.querySelector('input').addEventListener('change',()=> {
            toggleComplete(item.id);
        });
        divElement.querySelector('button').addEventListener('click',()=>{
            deleteTask(item.id)
        });

        resultElement.appendChild(divElement);
                                  
    });
}

document.getElementById('addBtn').addEventListener('click', () => {
    // get input value 
    let value = document.getElementById('taskInput').value; // Fix typo in 'taskInput'
    if (value.trim() === '') return; // Prevent adding empty tasks
    let obj = {id: Date.now(),name: value, isCompleted: false};
    //push value array
    tasks.push(obj);// Use 'tasks' instead of 'task'
    localStorage.setItem('task',JSON.stringify(tasks));
    renderTasks(tasks)
    document.getElementById('taskInput').value = ''; // Clear input after adding
});

function deleteTask(taskID){
    tasks = tasks.filter(item => item.id !==taskID);
    localStorage.setItem('task',JSON.stringify(tasks));
    renderTasks(tasks);
 }
 document.getElementById('searchInput').addEventListener('keyup', () => {
    let searchKey = document.getElementById('searchInput').value.toLowerCase();
    let searchTasks = tasks.filter(item => item.name.toLowerCase().includes(searchKey));
    renderTasks(searchTasks);
});

function toggleComplete(taskID) {
    tasks = tasks.map((item) => 
        item.id === taskID ? { ...item, isCompleted: !item.isCompleted } : item
    );
    localStorage.setItem('tasks', JSON.stringify(tasks));
    renderTasks(tasks);
}

// Ensure tasks are displayed when the page loads
renderTasks(tasks);