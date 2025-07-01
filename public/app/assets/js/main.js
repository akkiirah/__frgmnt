/*
 * Licensed under JNK 1.1 — an anti-capitalist, share-alike license.
 *
 * Freely remix, learn, rebuild — just don’t sell or lock it down.
 * Derivatives must stay free and link back to the source.
 *
 * Full license: https://dstwre.sh/license
 */

window.addEventListener("DOMContentLoaded", () => {

    if (document.getElementById("dstwre")) {
        const dstwreEl = document.getElementById("dstwre");
        const descEl = document.getElementById("desc");

        [dstwreEl, descEl].forEach((el) => {
            const text = el.innerText.trim();
            el.setAttribute("data-text", text);
            el.textContent = "";
        });

        function typewriter(element, speed = 100, callback = null, addSlash = false) {
            const fullText = element.getAttribute("data-text") || "";
            element.textContent = "";

            let index = 0;
            const typer = setInterval(() => {
                index++;
                const currentSlice = fullText.slice(0, index);
                element.textContent = currentSlice;
                element.setAttribute("data-text", currentSlice);

                if (index === fullText.length) {
                    clearInterval(typer);

                    if (addSlash) {
                        const slash = document.createElement("span");
                        slash.classList.add("cursor", "chromatic");
                        slash.textContent = "/";
                        slash.setAttribute("data-text", slash.textContent);

                        setTimeout(() => {
                            element.appendChild(slash);
                        }, speed * 2);
                    }

                    if (typeof callback === "function") {
                        setTimeout(callback, speed * 2);
                    }
                }
            }, speed);
        } 
        typewriter(
            dstwreEl,
            100,
            () => {
                typewriter(descEl, 100, null, false);
            },
            true
        );
    }
});