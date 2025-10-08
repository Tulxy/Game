<?php

?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <title>Index</title>
</head>
<body class="bg-dark text-white">

<canvas id="canvas" width="1700" height="800" style="border: 2px black;"></canvas>
<h1 class="d-flex p-2 bg-primary justify-content-center align-items-center">
  SCORE: <span class="m-2 p-2" id="score">0</span>
  <span class="m-2 p-2">Collect 25 points to win</span>
  <span class="m-2 p-2">TIME: <span id="time">0 </span></span>
  <span class="m-2 p-2">BEST TIME: <span id="bestTime">0</span></span>
</h1>

<button class="replayBtn btn btn-primary p-2 ms-3">Play again</button>

<ul>

</ul>

<script>
  const canvas = document.getElementById("canvas");
  const ctx = canvas.getContext("2d");

  let score = -1;
  let gameOver = false;
  let special = false;
  let points = 0;

  let isLoopRunning = false;

  let elapsedTime = 0;
  let timerInterval = null;


  // Physics
  const gravity = 1.2;
  const jumpForce = -30;

  // Movement
  document.addEventListener("keydown", e => {
    const moveAmount = 15;

    if (e.key === "d" && player.x + player.perimeter + moveAmount <= border.x + border.width) {
      player.vx = moveAmount;
    }
    if (e.key === "a" && player.x - player.perimeter - moveAmount >= border.x) {
      player.vx = -moveAmount;
    }
    if (e.key === "w" && player.isOnGround) {
      player.vy = jumpForce;
      player.isOnGround = false;
    }

  });

  document.addEventListener("keyup", e => {
    if (e.key === "d" || e.key === "a") {
      player.vx = 0;
    }
  });

  // All parts of canvas
  const point = {
    x: 200,
    y: 200,
    width: 100,
    height: 100
  }

  const player = {
    x: 200,
    y: 700,
    perimeter: 50,
    vy: 0,
    vx: 0,
    isOnGround: false
  }

  const border = {
    x: 0,
    y: 0,
    width: 1690,
    height: 800
  }

  const ground = {
    x: 0,
    y: 800,
    start: 0,
    end: 1690
  }

  const jumper = {
    x: canvas.clientWidth / 2 - 300,
    y: ground.y - 300,
    width: 600,
    height: 20
  };

  // All functions of the game
  document.querySelector(".replayBtn").addEventListener("click", () => {
    score = 0;
    points = 0;
    elapsedTime = 0;
    gameOver = false;
    document.getElementById("score").textContent = score;
    document.getElementById("time").textContent = elapsedTime;

    // Reset player
    player.x = 200;
    player.y = 700;
    player.vx = 0;
    player.vy = 0;
    player.isOnGround = false;

    // Restart timer
    clearInterval(timerInterval);
    startTimer();

    collectPoint();
    gameLoop();
  });

  function isColliding(circle, rect) {
    const closestX = Math.max(rect.x, Math.min(circle.x, rect.x + rect.width));
    const closestY = Math.max(rect.y, Math.min(circle.y, rect.y + rect.height));

    const dx = circle.x - closestX;
    const dy = circle.y - closestY;

    return (dx * dx + dy * dy) < (circle.perimeter * circle.perimeter);
  }

  function collectPoint() {
    score += 1;
    points += 1;
    document.getElementById('score').textContent = score;

    point.x = Math.random() * (border.width - point.width);
    point.y = Math.random() * (border.height - point.height);
  }

  function collectSpecialPoint() {
    score += 3;
    points += 1;
    document.getElementById('score').textContent = score;

    point.x = Math.random() * (border.width - point.width);
    point.y = Math.random() * (border.height - point.height);
  }

  function drawBorder() {
    ctx.beginPath();
    ctx.rect(border.x, border.y, border.width, border.height);
    ctx.lineWidth = 1;
    ctx.fillStyle = "#6bdbff";
    ctx.fillRect(border.x, border.y, border.width, border.height);
    ctx.strokeStyle = "#000000";
    ctx.stroke();
  }

  function drawPlayer() {
    ctx.beginPath();
    ctx.arc(player.x, player.y, player.perimeter, 0, 2 * Math.PI);
    ctx.fillStyle = "#ff0000";
    ctx.fill();
    ctx.strokeStyle = "#ff0000";
    ctx.stroke();
  }

  function spawnPoint() {
    ctx.beginPath();
    ctx.arc(point.x + point.width / 2, point.y + point.height / 2, point.width / 2, 0, Math.PI * 2);
    ctx.fillStyle = "gold";
    ctx.fill();
    ctx.strokeStyle = "#b8860b";
    ctx.stroke();
  }

  function spawnSpecialPoint() {
    ctx.beginPath();
    ctx.arc(point.x + point.width / 2, point.y + point.height / 2, point.width / 2, 0, Math.PI * 2);
    ctx.fillStyle = "darkorange";
    ctx.fill();
    ctx.strokeStyle = "#ff4600";
    ctx.stroke();
  }

  function setGround() {
    ctx.beginPath();
    ctx.moveTo(ground.start, ground.y);
    ctx.lineTo(ground.end, ground.y);
    ctx.strokeStyle = "#0d7700";
    ctx.lineWidth = 100;
    ctx.stroke()
  }

  function spawnJumper() {
    ctx.fillStyle = "#333";
    ctx.fillRect(jumper.x, jumper.y, jumper.width, jumper.height);
  }

  function updatePlayer() {
    player.x += player.vx;

    if (player.x - player.perimeter < border.x) {
      player.x = border.x + player.perimeter;
    }
    if (player.x + player.perimeter > border.width) {
      player.x = border.width - player.perimeter;
    }
    player.vy += gravity;
    player.y += player.vy;

    const groundY = ground.y - (player.perimeter * 2);

    if (player.y >= groundY) {
      player.y = groundY;
      player.vy = 0;
      player.isOnGround = true;
    }

    if (
      player.y + player.perimeter >= jumper.y &&
      player.y + player.perimeter <= jumper.y + jumper.height &&
      player.x + player.perimeter > jumper.x &&
      player.x - player.perimeter < jumper.x + jumper.width &&
      player.vy >= 0
    ) {
      player.y = jumper.y - player.perimeter;
      player.vy = 0;
      player.isOnGround = true;
    }

  }

  function win() {
    clearInterval(timerInterval);

    const newTime = document.createElement('li');
    newTime.classList.add('time');
    newTime.textContent = `Time: ${elapsedTime}s`;

    const timesList = document.querySelector('ul');
    timesList.appendChild(newTime);

    const bestTimeElement = document.getElementById('bestTime');
    const currentBestTime = parseInt(bestTimeElement.textContent);
    if (currentBestTime === 0 || elapsedTime < currentBestTime) {
      bestTimeElement.textContent = elapsedTime;
    }

    ctx.font = "120px Arial";
    ctx.textAlign = "center";
    ctx.fillText("You WIN!", (canvas.clientWidth / 2), (canvas.clientHeight / 2));
  }

  function startTimer() {
    timerInterval = setInterval(() => {
      elapsedTime++;
      document.getElementById('time').textContent = elapsedTime;
    }, 1000);
  }

  function gameLoop() {
    if (gameOver || isLoopRunning) return;

    isLoopRunning = true;

    function loop() {
      if (gameOver) {
        isLoopRunning = false;
        return;
      }

      ctx.clearRect(0, 0, canvas.clientWidth, canvas.clientHeight);

      updatePlayer();
      drawBorder();
      drawPlayer();
      spawnPoint();

      if (points !== 0 && points % 5 === 0) {
        special = true;
        spawnSpecialPoint();
        if (isColliding(player, point)) {
          collectSpecialPoint();
        }
      } else {
        special = false;
        spawnPoint();
        if (isColliding(player, point)) {
          collectPoint();
        }
      }

      spawnJumper();
      setGround();

      if (score >= 25) {
        win();
        gameOver = true;
        isLoopRunning = false;
        return;
      }

      requestAnimationFrame(loop);
    }

    requestAnimationFrame(loop);
  }


  startTimer();
  collectPoint();
  gameLoop();


</script>

</body>
</html>
