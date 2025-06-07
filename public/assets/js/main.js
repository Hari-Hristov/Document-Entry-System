// Валидации при качване и проверка
document.addEventListener("DOMContentLoaded", () => {
    const uploadForm = document.getElementById("uploadForm");
    const checkForm = document.getElementById("checkForm");

    uploadForm?.addEventListener("submit", (e) => {
        const file = document.getElementById("document").files[0];
        if (!file) {
            alert("Моля, изберете файл за качване.");
            e.preventDefault();
        }
    });

    checkForm?.addEventListener("submit", (e) => {
        const incoming = document.getElementById("incoming").value.trim();
        const code = document.getElementById("access_code").value.trim();
        if (!incoming || !code) {
            alert("Моля, въведете валидни входящ номер и код.");
            e.preventDefault();
        }
    });
});
