// Função para criar estrelas
function createStar() {
  const star = document.createElement("div");
  star.className = "star";

  // Tamanho aleatório da estrela
  const size = Math.random() * 3 + 1;
  star.style.width = size + "px";
  star.style.height = size + "px";

  // Posição aleatória
  star.style.left = Math.random() * 100 + "%";
  star.style.top = Math.random() * 100 + "%";

  // Delay aleatório para animação
  star.style.animationDelay = Math.random() * 2 + "s";

  return star;
}

// Função para criar estrela divertida em formato de estrela de 5 pontas
function createFunStar() {
  const funStar = document.createElement("div");
  funStar.className = "fun-star";

  const starShape = document.createElement("div");
  starShape.className = "star-5-points";
  funStar.appendChild(starShape);

  // Posição aleatória
  funStar.style.left = Math.random() * 90 + "%";
  funStar.style.top = Math.random() * 90 + "%";

  // Delay aleatório para animação
  funStar.style.animationDelay = Math.random() * 3 + "s";

  return funStar;
}

// Adicionar estrelas ao céu
const sky = document.getElementById("sky");

// Criar 150 estrelas normais
for (let i = 0; i < 150; i++) {
  sky.appendChild(createStar());
}

// Criar 20 estrelas divertidas
for (let i = 0; i < 20; i++) {
  sky.appendChild(createFunStar());
}

// Adicionar mais estrelas dinamicamente
setInterval(() => {
  if (sky.children.length < 200) {
    const rand = Math.random();
    if (rand > 0.7) {
      sky.appendChild(createFunStar());
    } else {
      sky.appendChild(createStar());
    }
  }
}, 3000);

// Efeito de clique para criar estrelas coloridas e divertidas
sky.addEventListener("click", (e) => {
  const funStar = document.createElement("div");
  funStar.className = "fun-star";

  const starShape = document.createElement("div");
  starShape.className = "star-5-points";
  funStar.appendChild(starShape);

  funStar.style.position = "fixed";
  funStar.style.left = e.clientX - 10 + "px";
  funStar.style.top = e.clientY - 10 + "px";
  funStar.style.pointerEvents = "none";
  funStar.style.zIndex = "1000";
  funStar.style.animation =
    "bounce 1s ease-out, colorChange 2s linear infinite";

  document.body.appendChild(funStar);

  // Remove a estrela após 4 segundos
  setTimeout(() => {
    if (funStar.parentNode) {
      funStar.parentNode.removeChild(funStar);
    }
  }, 4000);
});

// Adicionar efeito de paralaxe suave ao mover o mouse
document.addEventListener("mousemove", (e) => {
  const stars = document.querySelectorAll(".star");
  const mouseX = e.clientX / window.innerWidth;
  const mouseY = e.clientY / window.innerHeight;

  stars.forEach((star, index) => {
    const speed = ((index % 3) + 1) * 0.5;
    const x = (mouseX - 0.5) * speed;
    const y = (mouseY - 0.5) * speed;

    star.style.transform = `translate(${x}px, ${y}px)`;
  });
});
