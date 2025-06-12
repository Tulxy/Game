<?php
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Index</title>
</head>
<body>

<canvas id="canvas" width="1700" height="800" style="border: 2px black;"></canvas>
<h1 style="display: flex; justify-content: center">SCORE: <span id="score"> 0</span><span style="margin-left: 10px">Collect 25 points to win</span></h1>


<script>
  const canvas = document.getElementById("canvas");
  const ctx = canvas.getContext("2d");

  let score = -1;
  let gameOver = false;

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

  function isColliding(circle, rect) {
    const closestX = Math.max(rect.x, Math.min(circle.x, rect.x + rect.width));
    const closestY = Math.max(rect.y, Math.min(circle.y, rect.y + rect.height));

    const dx = circle.x - closestX;
    const dy = circle.y - closestY;

    return (dx * dx + dy * dy) < (circle.perimeter * circle.perimeter);
  }

  function collectPoint() {
    score += 1;
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
    ctx.fillStyle = "orange";
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
    ctx.font = "120px Arial";
    ctx.textAlign = "center";
    ctx.fillText("You WIN!", (canvas.clientWidth / 2), (canvas.clientHeight / 2));
  }

  function gameLoop() {
    if (gameOver) return;

    ctx.clearRect(0, 0, canvas.clientWidth, canvas.clientHeight);

    updatePlayer();
    drawBorder();
    drawPlayer();
    spawnPoint();
    spawnSpecialPoint();
    spawnJumper();
    setGround();

    if (isColliding(player, point)) {
      collectPoint();
    }

    if (score === 25) {
      win();
      gameOver = true;
      return;
    }
    requestAnimationFrame(gameLoop);
  }

  collectPoint();
  gameLoop();


</script>

</body>
</html>
