document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.edit-message-btn');
    const editMessageModal = document.getElementById('editMessageModal');
    const closeEditModal = document.getElementById('closeEditModal');
    const editMessageId = document.getElementById('editMessageId');
    const editMessageContent = document.getElementById('editMessageContent');

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const messageId = this.getAttribute('data-id');
            const messageContent = this.getAttribute('data-message');

            editMessageId.value = messageId;
            editMessageContent.value = messageContent;

            editMessageModal.style.display = 'block';
        });
    });

    closeEditModal.addEventListener('click', function () {
        editMessageModal.style.display = 'none';
    });

    window.onclick = function (event) {
        if (event.target == editMessageModal) {
            editMessageModal.style.display = 'none';
        }
    };
});