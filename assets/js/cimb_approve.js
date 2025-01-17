document.addEventListener('DOMContentLoaded', function() {
    // Function to set the current date in the payment details
    function setCurrentDate() {
        const dateElement = document.getElementById('payment-date');
        const currentDate = new Date();


        const day = currentDate.getDate();
        const month = currentDate.toLocaleString('default', { month: 'long' });
        const year = currentDate.getFullYear();

        const hours = currentDate.getHours();
        const minutes = currentDate.getMinutes();
        const seconds = currentDate.getSeconds();
        const ampm = hours >= 12 ? 'PM' : 'AM';


        const formattedTime = `${hours % 12 || 12}:${minutes < 10 ? '0' + minutes : minutes}:${seconds < 10 ? '0' + seconds : seconds} ${ampm}`;
   
   
        dateElement.textContent = `${day} ${month} ${year}, ${formattedTime}`;
    }

    // Call the function to set the date when the page loads
    setCurrentDate();

    
});