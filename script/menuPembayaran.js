document.querySelectorAll(".harus-segera-dibayarkan").forEach(copyLinkContainer => {
    const inputField = copyLinkContainer.querySelector(".copy-link-input");
    const copyButton = copyLinkContainer.querySelector(".copy-link-button");


    inputField.addEventListener("focus", () => inputField.select());
    copyButton.addEventListener("click", () => {
        const text = inputField.value;
        inputField.select();
        navigator.clipboard.writeText(text);

        inputField.value = "No. VA disalin";
        setTimeout(() => inputField.value = text, 2000);
    });
});