let bookings; 

document.addEventListener("DOMContentLoaded", function () {
    const timetableBody = document.querySelector("#timetable tbody");
    const startHour = 6;
    const endHour = 20; 
    bookings = Array(7).fill().map(() => Array(endHour - startHour + 1).fill(false)); 

    for (let hour = startHour; hour <= endHour; hour++) {
        const row = document.createElement("tr");
        
       
        const timeCell = document.createElement("td");
        timeCell.textContent = `${hour % 12 || 12} ${hour < 12 ? 'AM' : 'PM'} - ${((hour + 1) % 12 || 12)} ${hour + 1 < 12 ? 'AM' : 'PM'}`;
        row.appendChild(timeCell);

      
        for (let day = 0; day < 7; day++) {
            const cell = document.createElement("td");
            const button = document.createElement("button");
            button.textContent = "Book";
            button.disabled = bookings[day][hour - startHour];
            button.onclick = () => bookSlot(hour, day, button);
            cell.appendChild(button);
            row.appendChild(cell);
        }

        timetableBody.appendChild(row);
    }
});

function bookSlot(hour, day, button) {
    const dayNames = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
    const time = `${hour % 12 || 12} ${hour < 12 ? 'AM' : 'PM'} - ${((hour + 1) % 12 || 12)} ${hour + 1 < 12 ? 'AM' : 'PM'}`;
    
    if (confirm(`Confirm booking for ${time} on ${dayNames[day]}?`)) {
      
        bookings[day][hour - startHour] = true;
        
       
        button.disabled = true;
        button.textContent = "Booked"; 
        button.style.backgroundColor = "#ccc"; 
    }
}