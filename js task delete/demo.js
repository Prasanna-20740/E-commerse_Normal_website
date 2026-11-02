// user can create their tasks -completed
// user can delete a task - completed
// user can able to search task 
// user can update a task as complete and undo

let tasks = []; // Make sure the array name matches everywhere
let resultElement = document.getElementById('result')

function renderTasks(array){
     // reset result element
    resultElement.innerHTML =''
     // append all values in result
     tasks.forEach((item ,index) =>{
        resultElement.innerHTML += `<div>
                                     <h1><input type="checkbox" onchange="toggleComplete(${item.id})" ${item.isCompleted ? "checked": ""}>${item.name} - ${item.isCompleted ?"Completed": ""}</h1>
                                     <button onclick="deleteTask(${item.id})">Delete</button>
                                     </div>`})
}

document.getElementById('addBtn').addEventListener('click', () => {
    // get input value 
    let value = document.getElementById('taskInput').value; // Fix typo in 'taskInput'
    let obj = {id: Date.now(),name: value, iscompleted: false}
    //push value array
    tasks.push(obj);// Use 'tasks' instead of 'task'
    renderTasks(tasks)
});

function deleteTask(taskID){
    tasks = tasks.filter(item => item.id !=taskID)
    renderTasks(tasks)
 }

 document.getElementById('searchInput').addEventListener('keyup',()=>{

    let searchKey = document.getElementById('searchInput').value; // fix typo in 'searchInput'

   let searchTasks= tasks.filter(item =>
    {
        return item.name.includes(searchKey)
    }
    )
    
    renderTasks(searchTasks)
 })

function toggleComplete(taskID){
    tasks=tasks.map((item)=>{
        if(item.id== taskID){
            item.isCompleted=!item.isCompleted
            return item
        }
        else{
            return item
        }
    
        

    })
    renderTasks(tasks)
}