window.addEventListener("DOMContentLoaded", function() {
    const toggleLogin = document.querySelector(".connect");
    if (sessionStorage.getItem("isConnected") === "true") {
        toggleLogin.innerText = "Déconnecter";
        toggleLogin.style.display = "block";
        document.getElementById("loginBox").classList.add("hidden");
    }
});
document.querySelector(".about").addEventListener("click", page2);
document.querySelector(".home").addEventListener("click", page1);
document.querySelector(".cat").addEventListener("click", page3);
document.querySelector(".contact").addEventListener("click", page4);

function page2(){    
 document.querySelector(".page2").scrollIntoView({ behavior: "smooth" });
}
function page1(){    
    document.querySelector(".page1").scrollIntoView({ behavior: "smooth" });
}
function page3(){    
    document.querySelector(".page3").scrollIntoView({ behavior: "smooth" });
}
function page4(){    
    document.querySelector(".page4").scrollIntoView({ behavior: "smooth" });
}

function afficher() {
    const toggleLogin = document.querySelector(".connect");
    const loginBox = document.getElementById("loginBox");

    if (toggleLogin.innerText === "Connecter") {
        toggleLogin.style.display = "none";
        loginBox.classList.remove("hidden");
    } 
}


document.getElementById("loginForm").addEventListener("submit", function(event) {
    event.preventDefault(); 

    const formData = new FormData(this);

    fetch("login.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (data.role === "admin") {
                window.location.href = "admin.php"; 
            } else {
             
                const toggleLogin = document.querySelector(".connect");
                toggleLogin.innerText = "Déconnecter";
                toggleLogin.style.display = "block";
                document.getElementById("loginBox").classList.add("hidden");
    
             
               sessionStorage.setItem("isConnected", "true");
            }
        } else {
            alert(data.message);
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        }
    })
    
});
document.querySelector(".connect").addEventListener("click", function() {
    if (this.innerText === "Déconnecter") {
       
        sessionStorage.removeItem("isConnected");

       
        this.innerText = "Connecter";
       
        location.reload();
    } else {
        afficher(); 
    }
});



function afficherParCategorie(categorie, containerId, sectionClass) {
    const page = document.getElementById(containerId);
    page.innerHTML = '<div class="main"></div>'; // Vide le conteneur

    const container = page.querySelector('.main');

  
    container.style.display = 'flex';
    container.style.flexDirection = 'row';
    container.style.flexWrap = 'wrap';
    container.style.gap = '20px';
    container.style.justifyContent = 'center';
    if (page.style.display === "none") {
        fetch("get_items.php")  
            .then(response => response.json())
            .then(data => {
               
                data.forEach(item => {
                    if (item.categorie === categorie) {
                        const div = document.createElement("div");
                        div.classList.add("itm");
                        div.innerHTML = `
                            <div class="haut">
                                <img src="${item.imageSrc}"  class="im" >
                            </div>
                            <div class="bas">
                                <p class="description">${item.nom}</p>
                                <p class="pargh" style="display: none;">${item.description}</p>
                                <h5>${item.prix} DA</h5>
                               <button class="btnselect" data-id=${item.item_id}>select option</button>                               
                            </div>
                        `;
                        container.appendChild(div);
                        div.querySelector(".btnselect").addEventListener("click", function () {
                         
                             const itemId = this.getAttribute("data-id");
                             
                             window.location.href = `details.php?id=${itemId}`;
                        });
                    }
                });
                page.style.display = "block";
                document.querySelector("." + sectionClass).scrollIntoView({ behavior: "smooth" });
            })
            .catch(error => console.error("Erreur lors du fetch :", error));
    } else {
        page.style.display = "none";
        document.querySelector(".page3").scrollIntoView({ behavior: "smooth" });
    }
}

document.getElementById("catg1").addEventListener("click", function() {
    afficherParCategorie("keyboard & mouse set", "page7", "page7");
});
document.getElementById("catg2").addEventListener("click", function() {
    afficherParCategorie("pc models", "page6", "page6");
});
document.getElementById("catg3").addEventListener("click", function() {
    afficherParCategorie("premium headphones", "page5", "page5");
});


