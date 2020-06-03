let app = {

    init: function(){
        // Url adress
        let currentUrl = document.location.href; 

        //We delete the last / in the url if we have it
        let newCurrentUrl = currentUrl.replace(/\/$/, "");

        // We get the slug behind the last / in the new current Url
        if (newCurrentUrl.indexOf( "?" ) == -1) {
            slugUrl = newCurrentUrl.substring(newCurrentUrl.lastIndexOf( "/" )+1);
        } else {
            slugUrl = newCurrentUrl.substring(newCurrentUrl.lastIndexOf( "/" )+1, newCurrentUrl.indexOf( "?" ));
        }

        // If the length of the node list is different of 0 we can lunch the method to have the filter on the left menu 
        // or the selector is not find in the DOM
        if(document.querySelectorAll('.left-nav-label-category').length !== 0){

            app.leftMenuOnClick();
        }
    },

    // Method to filter the left menu, depend on where we are on the website
    leftMenuOnClick: function(){

        // We recover all of the node of the <label> category in the DOM
        let categoryLabelElement = document.querySelectorAll('.left-nav-label-category');

        //We made a loop of the last result
        for(let i = 0 ; i < categoryLabelElement.length ; i++){
            //console.log(categoryLabelElement[i]);

            // If the slug in the url and the dataset in the label are egal we come in
            if(slugUrl === categoryLabelElement[i].dataset.category){
                //console.log(categoryLabelElement[i]);

                // We can configure made a click on the plus icon and put the label bold for the result who passed the condition
                categoryLabelElement[i].querySelector('.left-nav-label-category .fa-plus').click();
                categoryLabelElement[i].style.fontWeight = 'bold';
                
                // We can breack the loop after that
                break;
            }
        }

        // We recover all of the node of the <label> sub category in the DOM
        let subCategoryLabelElement = document.querySelectorAll('.left-nav-label-subCategory');
        //We made a loop of the last result
        for(let i = 0 ; i < subCategoryLabelElement.length ; i++){
            //console.log(subCategoryLabelElement[i].dataset.subcategory);

            // If the slug in the url and the dataset in the label are egal we come in
            if(slugUrl === subCategoryLabelElement[i].dataset.subcategory){
                //console.log(subCategoryLabelElement[i].dataset.subcategory)

                // We can configure made a click on the plus icon and put the label bold for the result who passed the condition
                subCategoryLabelElement[i].querySelector('.left-nav-label-subCategory .fa-plus').click();
                subCategoryLabelElement[i].style.fontWeight = 'bold';
                
                // We can configure made a click on the plus icon and put the label bold for the result who passed the condition
                // and who has the category parent of the sub-category
                subCategoryLabelElement[i].parentElement.parentElement.parentElement.querySelector('.left-nav-label-category .fa-plus').click();
                subCategoryLabelElement[i].parentElement.parentElement.parentElement.querySelector('.left-nav-label-category').style.fontWeight = 'bold';

                break;
            }
        }

        // We recover all of the node of the <li> type in the DOM
        let typeLiElement = document.querySelectorAll('.left-nav-li-type');
        //We made a loop of the last result
        for(let i = 0 ; i < typeLiElement.length ; i++){
            //console.log(typeLiElement[i].dataset.type);

            // If the slug in the url and the dataset in the li are egal we come in
            if(slugUrl === typeLiElement[i].dataset.type){     

                // We can configure the link of the type bold for the result who passed the condition
                typeLiElement[i].querySelector('.left-nav-link-type').style.fontWeight = 'bold';

                // We can configure made a click on the plus icon and put the label bold for the result who passed the condition
                // and who has the sub category parent of the type
                typeLiElement[i].parentElement.parentElement.querySelector('.left-nav-label-subCategory .fa-plus').click();
                typeLiElement[i].parentElement.parentElement.querySelector('.left-nav-label-subCategory').style.fontWeight = 'bold';

                // We can configure made a click on the plus icon and put the label bold for the result who passed the condition
                // and who has the category parent of the sub-category
                typeLiElement[i].parentElement.parentElement.parentElement.parentElement.querySelector('.left-nav-label-category .fa-plus').click()
                typeLiElement[i].parentElement.parentElement.parentElement.parentElement.querySelector('.left-nav-label-category').style.fontWeight = 'bold';

                break;
            }
        }
    }

}

// On veut exécuter app.init une fois que la page chargée
document.addEventListener('DOMContentLoaded', app.init);