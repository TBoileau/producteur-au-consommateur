import './styles/app.scss';
import 'popper.js';
import 'bootstrap';

$(".btn-remove-file").on("click", e => {
    console.log("ici");
    $(e.currentTarget.dataset.target).val('');
});

