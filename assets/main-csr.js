const dataTable=document.getElementById('dataTable');
const pageHeader=document.getElementById('pageHeader');
const categoryForm=document.getElementById('categoryForm');
const propertyForm=document.getElementById('propertyForm');
const ebookForm=document.getElementById('ebookForm');
const ebookLink=document.getElementById('ebookLink');
const categoryLink=document.getElementById('categoryLink');
const propertyLink=document.getElementById('propertyLink');
const logoutLink=document.getElementById('logoutLink');
const categorySelect=document.getElementById('ebookCategoryInput');
const contentContainer=document.getElementById('contentContainer');
const loginContainer=document.getElementById('loginContainer');
const loginForm=document.getElementById('loginForm');
const loginErrorText=document.getElementById('loginErrorText');
function checkLogin(){
    fetch('http://localhost/lab/app/api/loginController.php')
    .then((response) => {
        return response.json();
    })
    .then((data) => {
        console.log(data);
        if(data.userlogin==''){
            loginContainer.style.display='flex';
        }else{
            getCategories();
            getProperties();
            getEbooks();
            contentContainer.style.display='flex';
        }
    });
}
function getCategories(){
    fetch('http://localhost/lab/app/api/categoryController.php')
    .then((response) => {
        return response.json();
    })
    .then((data) => {
        [].forEach.call(document.querySelectorAll('.app-form'), function (el) {
            el.style.display = 'none';
          });
        categoryForm.style.display='block';
        //console.log(data);
        pageHeader.innerText='Категорії';
        let content=``;
        let selectContent=``;
        for (let i=0;i<data.length;i++){
            selectContent+=`<option value="`+data[i].name+`">`+data[i].name+`</option>`
            content+=`<tr>
                        <td>`+data[i].id+`</td>
                        <td>`+data[i].name+`</td>
            </tr>`;
        }
        dataTable.innerHTML=`<thead>
                                <th>ID</th>
                                <th>Назва</th>
                            </thead>
                            <tbody>
                                `+content+`
                            </tbody>`;
        categorySelect.innerHTML=selectContent;
    });
}
function getProperties(){
    fetch('http://localhost/lab/app/api/propertyController.php')
    .then((response) => {
        return response.json();
    })
    .then((data) => {
        //console.log(data);
        [].forEach.call(document.querySelectorAll('.app-form'), function (el) {
            el.style.display = 'none';
          });
        propertyForm.style.display='block';
        pageHeader.innerText='Характеристики';
        let content=``;
        for (let i=0;i<data.length;i++){
            content+=`<tr>
                        <td>`+data[i].id+`</td>
                        <td>`+data[i].name+`</td>
                        <td>`+data[i].units+`</td>
            </tr>`;
        }
        dataTable.innerHTML=`<thead>
                                <th>ID</th>
                                <th>Назва</th>
                                <th>Одиниці вимірювання</th>
                            </thead>
                            <tbody>
                                `+content+`
                            </tbody>`;
    });
}
function getEbooks(){
    fetch('http://localhost/lab/app/api/ebookController.php')
    .then((response) => {
        return response.json();
    })
    .then((data) => {
        //console.log(data);
        [].forEach.call(document.querySelectorAll('.app-form'), function (el) {
            el.style.display = 'none';
          });
        ebookForm.style.display='block';
        pageHeader.innerText='Електроні книги';
        let content=``;
        for (let i=0;i<data.length;i++){
            let propertyContent=``;
            for (const [key, value] of Object.entries(data[i].properties)) {
                propertyContent+=key+`: `+value+`</br>`;
            }
            content+=`<tr>
                        <td>`+data[i].id+`</td>
                        <td>`+data[i].brand+`</td>
                        <td>`+data[i].model+`</td>
                        <td>`+data[i].category+`</td>
                        <td>`+data[i].price+`</td>
                        <td>`+propertyContent+`</td>
            </tr>`;
        }
        dataTable.innerHTML=`<thead>
                                <th>ID</th>
                                <th>Бренд</th>
                                <th>Модель</th>
                                <th>Категорія</th>
                                <th>Ціна</th>
                                <th>Характеристики</th>
                            </thead>
                            <tbody>
                                `+content+`
                            </tbody>`;
    });
}
checkLogin();

categoryForm.addEventListener("submit", (event) => {
    event.preventDefault();
    let categoryName=document.getElementById('categoryNameInput').value;
    let formData = new FormData();
    formData.append('name', categoryName);
    fetch("http://localhost/lab/app/api/categoryController.php",
        {
            body: formData,
            method: "POST"
        }).then(()=>{
            categoryForm.reset();
            getCategories();
        });
  });
propertyForm.addEventListener("submit", (event) => {
    event.preventDefault();
    let propertyName=document.getElementById('propertyNameInput').value;
    let propertyUnits=document.getElementById('propertyUnitsInput').value;
    let formData = new FormData();
    formData.append('name', propertyName);
    formData.append('units', propertyUnits);
    fetch("http://localhost/lab/app/api/propertyController.php",
        {
            body: formData,
            method: "POST"
        }).then(()=>{
            propertyForm.reset();
            getProperties();
        });
  });
ebookForm.addEventListener("submit", (event) => {
    event.preventDefault();
    let ebookBrand=document.getElementById('ebookBrandInput').value;
    let ebookModel=document.getElementById('ebookModelInput').value;
    let ebookCategory=document.getElementById('ebookCategoryInput').value;
    let ebookPrice=document.getElementById('ebookPriceInput').value;
    let ebookProperties=document.getElementById('ebookPropertiesInput').value;
    let formData = new FormData();
    formData.append('brand', ebookBrand);
    formData.append('model', ebookModel);
    formData.append('category', ebookCategory);
    formData.append('price', ebookPrice);
    formData.append('properties', ebookProperties);
    fetch("http://localhost/lab/app/api/ebookController.php",
        {
            body: formData,
            method: "POST"
        }).then(()=>{
            ebookForm.reset();
            getEbooks();
        });
  });
loginForm.addEventListener("submit", (event) => {
    event.preventDefault();
    let login=document.getElementById('loginInput').value;
    let password=document.getElementById('passwordInput').value;
    let formData = new FormData();
    formData.append('login', login);
    formData.append('password', password);
    fetch("http://localhost/lab/app/api/loginController.php",
        {
            body: formData,
            method: "POST"
        }).then((response) => {
            return response.json();
        })
        .then((data) => {
            loginForm.reset();
            if(data.error==''){
                loginContainer.style.display='none';
                contentContainer.style.display='flex';
                getCategories();
                getProperties();
                getEbooks();
            } else{
                loginErrorText.innerText=data.error;
            }
        });
  });
EbookLink.addEventListener("click", (event) => {
    event.preventDefault();
    getEbooks();
  });
categoryLink.addEventListener("click", (event) => {
    event.preventDefault();
    getCategories();
  });
propertyLink.addEventListener("click", (event) => {
    event.preventDefault();
    getProperties();
  });
logoutLink.addEventListener("click", (event) => {
    event.preventDefault();
    fetch('http://localhost/lab/app/api/loginController.php?action=logout')
    .then((response) => {
        return response.json();
    })
    .then((data) => {
        loginContainer.style.display='flex';
        contentContainer.style.display='none';
        loginErrorText.innerText='';
    });
  });