import '../style/NavBar.scss'


const menuBtn = document.querySelector('.burger-btn')
const closeBtn = document.querySelector('.close-btn')
const menu = document.querySelector('.menu-list')
const shadow = document.querySelector('.shadow')
const navLinks = document.querySelectorAll('.nav-li')

const showMenu = ()=>{
    menuBtn.addEventListener('click', ()=> {
            menu.classList.toggle('menu-clicked')
            shadow.classList.toggle('shadow-active')
        }
    )
    closeBtn.addEventListener('click', ()=> {
            menu.classList.toggle('menu-clicked')
            shadow.classList.toggle('shadow-active')
        }
    )
    shadow.addEventListener('click', () =>{
            if(shadow.classList.contains('shadow-active')){
                menu.classList.toggle('menu-clicked')
                shadow.classList.toggle('shadow-active')
            }
        }
    )
}
showMenu()

navLinks.forEach(navLink => {
    const burger = document.querySelector('.navbar-toggler');

    navLink.addEventListener('click', ()=>{
        burger.click();
    })
});