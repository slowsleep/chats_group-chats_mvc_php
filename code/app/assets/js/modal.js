// Открытие модального окна
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add("active");

    // Проверяем, какое окно открылось, и загружаем контакты
    if (modalId === "createGroupChatModal" || modalId === "forwardMessageModal") {
        getUserContacts(modal); // Загружаем контакты каждый раз при открытии
        handleSearchOwnContactsForm(modal); // Обработка поиска контактов
    }
}

// Закрытие модального окна
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.remove("active");
}

// Обработка кнопкок закрытия модальных окон
document.querySelectorAll(".modal-close").forEach(function(closeBtn) {
    closeBtn.addEventListener("click", function() {
        const modal = closeBtn.closest(".modal");
        modal.classList.remove("active");
        clearSelectedUsers();
    });
});
