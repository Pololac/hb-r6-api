// alert("hello");

const response = await fetch("http://localhost:8000");      // "fetch" = méthode pour lancer une requête sur une API

const users = await response.json();

console.log(users);

const usersCount = document.getElementById("users-count");
const usersContainer = document.getElementById("users-container");

usersCount.innerText = `${users.length} utilisateurs`;

users.map((user) => {
  const userDiv = document.createElement("p");
  userDiv.innerText = user.name;

  usersContainer.appendChild(userDiv);
});