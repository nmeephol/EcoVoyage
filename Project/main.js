let numberInput = document.getElementById('inputamountjounal');
const incrementButton = document.getElementById('increment');
const decrementButton = document.getElementById('decrement');
let numberInputday = document.getElementById('inputamountdayjounal');
const incremendaytButton = document.getElementById('incrementday');
const decrementdayButton = document.getElementById('decrementday');
const showmodal = document.getElementById('showmodal');
const modal = document.getElementById('modal')

incrementButton.addEventListener('click', () => {
    numberInput.value = parseInt(numberInput.value) + 1;
});

decrementButton.addEventListener('click', () => {
    numberInput.value = parseInt(numberInput.value) - 1;
});

incremendaytButton.addEventListener('click', () => {
    numberInputday.value = parseInt(numberInputday.value) + 1;
});

decrementdayButton.addEventListener('click', () => {
    numberInputday.value = parseInt(numberInputday.value) - 1;
});

showmodal.addEventListener('click',()=>{
    modal.classList.add('show')
})
