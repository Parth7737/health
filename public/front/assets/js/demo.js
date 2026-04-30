$(document).ready(function () {

    $('.toggle-boxes').click(function () {
        const toggleDiv = $('.toggle-div');
        const button = $(this);

        toggleDiv.slideToggle(300, function () {
            if (toggleDiv.is(':visible')) {
                button.text('Show Less');
            } else {
                button.text('View More');
            }
        });
    });

    if (document.querySelector(".list-view-btn") && document.querySelector(".grid-view-btn")) {
        const listViewButton = document.querySelector(".list-view-btn");
        const gridViewButton = document.querySelector(".grid-view-btn");
        const gridViewBox = document.querySelector(".grid-view-box");
        const listViewBox = document.querySelector(".list-view-box");

        listViewButton.addEventListener("click", function () {
            listViewButton.classList.add("active");
            gridViewButton.classList.remove("active");
            // listViewBox.style.display = "flex";
            $(".list-view-box").css("display",'flex');
            $(".grid-view-box").css("display",'none');
            // gridViewBox.style.display = "none";
        });

        gridViewButton.addEventListener("click", function () {
            gridViewButton.classList.add("active");
            listViewButton.classList.remove("active");
            // gridViewBox.style.display = "flex";
            // listViewBox.style.display = "none";
            
            $(".list-view-box").css("display",'none');
            $(".grid-view-box").css("display",'flex');
        });
    }

    if (document.querySelector(".accordion")) {
        const accordionActiveFunction = function (e) {
            if (e.type == 'show.bs.collapse') {
                e.target.closest('.accordion-item').classList.add('active');
                e.target.closest('.accordion-item').previousElementSibling?.classList.add('previous-active');
            } else {
                e.target.closest('.accordion-item').classList.remove('active');
                e.target.closest('.accordion-item').previousElementSibling?.classList.remove('previous-active');
            }
        };

        const accordionTriggerList = [].slice.call(document.querySelectorAll('.accordion'));
        accordionTriggerList.forEach(function (accordionTriggerEl) {
            accordionTriggerEl.addEventListener('show.bs.collapse', accordionActiveFunction);
            accordionTriggerEl.addEventListener('hide.bs.collapse', accordionActiveFunction);
        });
    }
});

(function () {
    document.addEventListener('click', (event) => {
        if (event.target.closest('.file-upload-wrapper')) {
            const fileWrapper = event.target.closest('.file-upload-wrapper');
            const fileInput = fileWrapper.parentElement.querySelector('.file-input');
            fileInput.click();
        }
    
        if (event.target.closest('.remove-file-btn')) {
            const removeFileBtn = event.target.closest('.remove-file-btn');
            const section = removeFileBtn.closest('.file-upload-section');
            const fileInput = section.querySelector('.file-input');
            const uploadedFile = section.querySelector('.uploaded-file');
            const fileWrapper = section.querySelector('.file-upload-wrapper');
    
            fileInput.value = "";
            uploadedFile.classList.add('d-none');
            fileWrapper.classList.remove('d-none');
        }
    });
    
    document.addEventListener('change', (event) => {
        if (event.target.classList.contains('file-input')) {
            const fileInput = event.target;
            const section = fileInput.closest('.file-upload-section');
            const fileWrapper = section.querySelector('.file-upload-wrapper');
            const uploadedFile = section.querySelector('.uploaded-file');
            const fileName = section.querySelector('.file-name');
    
            const file = fileInput.files[0];
            if (file) {
                fileName.textContent = file.name;
                uploadedFile.classList.remove('d-none');
                fileWrapper.classList.add('d-none');
            }
        }
    });
    

    // if (document.querySelector('.wizard-numbered')) {
    //     const wizardNumbered = document.querySelector('.wizard-numbered');
    //     const wizardNumberedBtnNextList = [].slice.call(wizardNumbered.querySelectorAll('.btn-next'));
    //     const wizardNumberedBtnPrevList = [].slice.call(wizardNumbered.querySelectorAll('.btn-prev'));
    //     const wizardNumberedBtnSubmit = wizardNumbered.querySelector('.btn-submit');

    //     const numberedStepper = new Stepper(wizardNumbered, {
    //         linear: false
    //     });

    //     const steps = wizardNumbered.querySelectorAll('.step');
    //     if (steps.length > 0) {
    //         steps[0].classList.add('completed');
    //         steps[0].classList.add('disabled');

    //         steps[0].addEventListener('click', event => {
    //             event.preventDefault();
    //             event.stopImmediatePropagation();
    //         });

    //         if (steps[1]) {
    //             steps[1].classList.add('crossed');
    //             numberedStepper.to(2);
    //         }
    //     }

    //     wizardNumberedBtnNextList.forEach(wizardNumberedBtnNext => {
    //         wizardNumberedBtnNext.addEventListener('click', () => {
    //             numberedStepper.next();
    //         });
    //     });

    //     wizardNumberedBtnPrevList.forEach(wizardNumberedBtnPrev => {
    //         wizardNumberedBtnPrev.addEventListener('click', () => {
    //             numberedStepper.previous();
    //         });
    //     });

    //     wizardNumberedBtnSubmit.addEventListener('click', () => {
    //         alert('Submitted..!!');
    //     });
    // }

    // document.addEventListener("DOMContentLoaded", function () {
    //     const tabs = document.querySelectorAll(".nav-tabs .nav-link");
    //     const prevButton = document.getElementById("prev-btn");
    //     const nextButton = document.getElementById("next-btn");

    //     let activeIndex = 0;

    //     const updateButtons = () => {
    //         prevButton.disabled = activeIndex === 0;
    //         nextButton.textContent = activeIndex === tabs.length - 1 ? "Finish" : "Next";
    //     };

    //     const activateTab = (index) => {
    //         if (index >= 0 && index < tabs.length) {
    //             tabs[activeIndex].classList.remove("active");
    //             tabs[activeIndex].ariaSelected = "false";
    //             document.querySelector(tabs[activeIndex].dataset.bsTarget).classList.remove("show", "active");

    //             activeIndex = index;

    //             tabs[activeIndex].classList.add("active");
    //             tabs[activeIndex].ariaSelected = "true";
    //             document.querySelector(tabs[activeIndex].dataset.bsTarget).classList.add("show", "active");

    //             updateButtons();
    //         }
    //     };

    //     prevButton.addEventListener("click", () => {
    //         activateTab(activeIndex - 1);
    //     });

    //     nextButton.addEventListener("click", () => {
    //         if (activeIndex < tabs.length - 1) {
    //             activateTab(activeIndex + 1);
    //         } else {
    //             alert("Finished!"); // Replace with desired finish action
    //         }
    //     });

    //     // Disable direct tab click
    //     tabs.forEach((tab) => {
    //         tab.addEventListener("click", (e) => {
    //             e.preventDefault();
    //         });
    //     });

    //     // Initialize button states
    //     updateButtons();
    // });

})();
