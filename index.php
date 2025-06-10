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

<canvas id="canvas" width="1000" height="1000" style="border: 2px black"></canvas>
<h1>SCORE: <span id="score">0</span></h1>

<script>
  const canvas = document.getElementById("canvas");
  const ctx = canvas.getContext("2d");

  let score = -1;

  document.addEventListener("keydown", e => {
    if (e.key === "d") player.x += 40;
    if (e.key === "a") player.x -= 40;
    if (e.key === "w") player.y -= 40;
    if (e.key === "s") player.y += 40;
  })

  const point = {
    x: 200,
    y: 200,
    width: 100,
    height: 100
  }

  const player = {
    x: 200,
    y: 200,
    perimeter: 50,
  }


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

    point.x = Math.random() * (canvas.width - point.width);
    point.y = Math.random() * (canvas.height - point.height);
  }


  function gameLoop() {
    // Player
    ctx.clearRect(0, 0, canvas.clientWidth, canvas.clientHeight);
    ctx.beginPath();
    ctx.arc(player.x, player.y, player.perimeter, 0, 2 * Math.PI);

    ctx.fillStyle = "#ff0000";
    ctx.fill();

    ctx.strokeStyle = "#ff0000";
    ctx.stroke();

    // Point
    ctx.fillStyle = "#000";
    ctx.fillRect(point.x, point.y, point.width, point.height);

    // Add point
    if (isColliding(player, point)) {
      collectPoint();
    }

    requestAnimationFrame(gameLoop);
  }

  gameLoop();

</script>

</body>
</html>
