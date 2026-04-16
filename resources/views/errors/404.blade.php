<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 - Halaman Tidak Ditemukan | SIAKAD OSAKA</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
    background:#f9fafb;
    display:flex;flex-direction:column;
    align-items:center;justify-content:center;
    min-height:100vh;
    font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
    overflow:hidden;
    user-select:none;
}

/* Sky & Ground */
.sky{
    position:fixed;inset:0;
    background:linear-gradient(180deg,#e0f2fe 0%,#f0f9ff 60%,#f9fafb 100%);
    z-index:0;
}
.ground{
    position:fixed;bottom:0;left:0;right:0;
    height:3px;background:#374151;z-index:2;
}
.ground-line{
    position:fixed;bottom:3px;left:0;right:0;
    height:1px;background:#d1d5db;z-index:2;
}

/* Game container */
.game-wrap{
    position:relative;z-index:10;
    width:min(600px,90vw);
    text-align:center;
}

/* 404 text */
.err-code{
    font-size:clamp(60px,12vw,100px);
    font-weight:900;
    color:#1e293b;
    letter-spacing:-2px;
    line-height:1;
    margin-bottom:4px;
}
.err-msg{
    font-size:clamp(13px,3vw,16px);
    color:#64748b;
    margin-bottom:32px;
    font-weight:500;
}

/* Canvas game */
#gameCanvas{
    border-bottom:3px solid #374151;
    display:block;
    margin:0 auto;
    background:transparent;
    cursor:pointer;
    touch-action:none;
}

/* Score */
.score-wrap{
    display:flex;justify-content:space-between;
    align-items:center;
    padding:6px 0;
    font-size:12px;font-weight:700;
    color:#94a3b8;letter-spacing:1px;
    width:min(600px,90vw);
}
.score-hi { color:#6366f1; }

/* Start hint */
#hint{
    margin-top:16px;
    font-size:13px;color:#94a3b8;
    animation:blink 1.2s infinite;
}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}

/* Back button */
.btn-back{
    display:inline-flex;align-items:center;gap:8px;
    margin-top:20px;
    padding:10px 24px;
    background:#6366f1;color:#fff;
    border-radius:10px;font-size:14px;font-weight:700;
    text-decoration:none;border:none;cursor:pointer;
    transition:background .15s,transform .1s;
}
.btn-back:hover  { background:#4f46e5; }
.btn-back:active { transform:scale(.97); }

/* Clouds */
.cloud{
    position:fixed;
    background:#fff;
    border-radius:50px;
    opacity:.7;
    animation:moveClouds linear infinite;
}
@keyframes moveClouds{
    from{transform:translateX(110vw)}
    to{transform:translateX(-300px)}
}
</style>
</head>
<body>

<div class="sky"></div>

<!-- Clouds -->
<div class="cloud" style="width:80px;height:28px;top:12%;animation-duration:18s;animation-delay:-3s;"></div>
<div class="cloud" style="width:120px;height:36px;top:22%;animation-duration:25s;animation-delay:-10s;"></div>
<div class="cloud" style="width:60px;height:22px;top:8%;animation-duration:20s;animation-delay:-7s;"></div>

<div class="game-wrap">
    <div class="err-code">404</div>
    <div class="err-msg">Halaman tidak ditemukan &mdash; tapi kamu bisa main dulu!</div>

    <div class="score-wrap">
        <span>HI <span class="score-hi" id="hiScore">00000</span></span>
        <span id="score">00000</span>
    </div>

    <canvas id="gameCanvas" width="600" height="150"></canvas>

    <div id="hint">Tekan SPASI / Tap untuk mulai &amp; lompat</div>

    <a href="javascript:history.back()" class="btn-back">
        &#8592; Kembali ke halaman sebelumnya
    </a>
</div>

<div class="ground"></div>
<div class="ground-line"></div>

<script>
var canvas  = document.getElementById('gameCanvas');
var ctx     = canvas.getContext('2d');
var hint    = document.getElementById('hint');
var scorEl  = document.getElementById('score');
var hiEl    = document.getElementById('hiScore');

// Responsive canvas width
function resizeCanvas() {
    var w = Math.min(600, window.innerWidth * 0.9);
    canvas.width  = w;
    canvas.style.width = w + 'px';
}
resizeCanvas();
window.addEventListener('resize', resizeCanvas);

var STATE = { IDLE:0, RUNNING:1, DEAD:2 };
var state = STATE.IDLE;
var score = 0, hiScore = 0, frame = 0;
var speed = 5, scoreTimer = 0;

// Dino
var dino = {
    x: 60, y: 0, w: 40, h: 50,
    vy: 0, onGround: true,
    legFrame: 0, legTimer: 0,
    dead: false,
    get groundY() { return canvas.height - this.h - 3; },
    jump: function() {
        if (this.onGround) {
            this.vy = -14;
            this.onGround = false;
        }
    },
    update: function() {
        if (!this.onGround) {
            this.vy += 0.7;
            this.y += this.vy;
        }
        if (this.y >= this.groundY) {
            this.y = this.groundY;
            this.vy = 0;
            this.onGround = true;
        }
        if (this.onGround && !this.dead) {
            this.legTimer++;
            if (this.legTimer > 8) { this.legFrame ^= 1; this.legTimer = 0; }
        }
    },
    draw: function() {
        var x = this.x, y = this.y, w = this.w, h = this.h;
        var c = this.dead ? '#dc2626' : '#374151';

        // Body
        ctx.fillStyle = c;
        ctx.beginPath();
        ctx.roundRect(x, y+10, w, h-10, 4);
        ctx.fill();

        // Head
        ctx.beginPath();
        ctx.roundRect(x+14, y, w-4, 22, 4);
        ctx.fill();

        // Eye
        ctx.fillStyle = '#fff';
        ctx.beginPath();
        ctx.arc(x+w+1, y+7, 5, 0, Math.PI*2);
        ctx.fill();
        ctx.fillStyle = this.dead ? '#dc2626' : '#1e293b';
        ctx.beginPath();
        if (this.dead) {
            // X eyes
            ctx.font = 'bold 9px sans-serif';
            ctx.fillText('x', x+w-3, y+11);
        } else {
            ctx.arc(x+w+2, y+7, 2.5, 0, Math.PI*2);
            ctx.fill();
        }

        // Mouth
        if (!this.dead) {
            ctx.fillStyle = c;
            ctx.beginPath();
            ctx.roundRect(x+w+2, y+13, 6, 3, 2);
            ctx.fill();
        }

        // Legs
        ctx.fillStyle = c;
        if (this.onGround && !this.dead) {
            if (this.legFrame === 0) {
                ctx.fillRect(x+8,  y+h-12, 8, 14);
                ctx.fillRect(x+22, y+h-18, 8, 8);
            } else {
                ctx.fillRect(x+8,  y+h-18, 8, 8);
                ctx.fillRect(x+22, y+h-12, 8, 14);
            }
        } else {
            ctx.fillRect(x+8,  y+h-12, 8, 12);
            ctx.fillRect(x+22, y+h-12, 8, 12);
        }

        // Tail
        ctx.beginPath();
        ctx.moveTo(x, y+20);
        ctx.lineTo(x-12, y+28);
        ctx.lineTo(x-8, y+36);
        ctx.lineTo(x, y+32);
        ctx.closePath();
        ctx.fill();
    }
};
dino.y = dino.groundY;

// Obstacles
var obstacles = [];
var obstTimer = 0;
var obstInterval = 90;

function spawnObstacle() {
    var h = 30 + Math.random()*30;
    var w = 18 + Math.random()*14;
    var count = Math.random() < 0.3 ? 2 : 1;
    for (var i=0; i<count; i++) {
        obstacles.push({
            x: canvas.width + i*30,
            w: w, h: h,
            y: canvas.height - h - 3
        });
    }
}

function drawObstacle(o) {
    ctx.fillStyle = '#374151';
    // Trunk
    ctx.beginPath();
    ctx.roundRect(o.x, o.y, o.w, o.h, 3);
    ctx.fill();
    // Spikes on top
    ctx.beginPath();
    var sw = o.w/3;
    for (var i=0; i<3; i++) {
        var sx = o.x + i*sw;
        ctx.moveTo(sx, o.y);
        ctx.lineTo(sx + sw/2, o.y - 14);
        ctx.lineTo(sx + sw, o.y);
    }
    ctx.fill();
}

// Stars / ground marks
var marks = [];
for (var i=0; i<8; i++) {
    marks.push({ x: Math.random()*600, w: 10+Math.random()*20 });
}

function drawGround() {
    ctx.fillStyle = '#d1d5db';
    marks.forEach(function(m) {
        ctx.fillRect(m.x, canvas.height-3, m.w, 1);
        m.x -= speed * 0.3;
        if (m.x + m.w < 0) m.x = canvas.width + Math.random()*100;
    });
}

// Collision
function collides(a, b) {
    var pad = 6;
    return a.x+pad < b.x+b.w && a.x+a.w-pad > b.x &&
           a.y+pad < b.y+b.h && a.y+a.h-pad > b.y;
}

function fmtScore(n) {
    return String(Math.floor(n)).padStart(5,'0');
}

function startGame() {
    state = STATE.RUNNING;
    score = 0; frame = 0; speed = 5;
    obstacles = []; obstTimer = 0; obstInterval = 90;
    dino.dead = false; dino.y = dino.groundY; dino.vy = 0; dino.onGround = true;
    hint.style.display = 'none';
}

function gameOver() {
    state = STATE.DEAD;
    dino.dead = true;
    if (score > hiScore) { hiScore = score; hiEl.textContent = fmtScore(hiScore); }
    hint.textContent = 'Tekan SPASI / Tap untuk main lagi';
    hint.style.display = 'block';
}

function loop() {
    requestAnimationFrame(loop);
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawGround();

    if (state === STATE.IDLE) {
        dino.draw();
        return;
    }

    if (state === STATE.RUNNING) {
        frame++;
        speed += 0.002;

        // Score
        scoreTimer++;
        if (scoreTimer > 5) { score += 1; scoreTimer = 0; }
        scorEl.textContent = fmtScore(score);

        // Obstacles
        obstTimer++;
        if (obstTimer >= obstInterval) {
            spawnObstacle();
            obstTimer = 0;
            obstInterval = Math.max(50, 90 - Math.floor(speed));
        }

        // Move & draw obstacles
        for (var i = obstacles.length-1; i >= 0; i--) {
            obstacles[i].x -= speed;
            if (obstacles[i].x + obstacles[i].w < 0) { obstacles.splice(i,1); continue; }
            drawObstacle(obstacles[i]);
            if (collides(dino, obstacles[i])) { gameOver(); }
        }

        dino.update();
    }

    if (state === STATE.DEAD) {
        obstacles.forEach(function(o){ drawObstacle(o); });
    }

    dino.draw();
}

// Controls
function handleAction() {
    if (state === STATE.IDLE || state === STATE.DEAD) { startGame(); }
    else if (state === STATE.RUNNING) { dino.jump(); }
}

document.addEventListener('keydown', function(e) {
    if (e.code === 'Space' || e.code === 'ArrowUp') {
        e.preventDefault();
        handleAction();
    }
});
canvas.addEventListener('click',     handleAction);
canvas.addEventListener('touchstart', function(e){ e.preventDefault(); handleAction(); }, {passive:false});

loop();
</script>
</body>
</html>