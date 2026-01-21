<?php ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>CS109 DFA Smart Streetlight Simulator</title>
    <style>
        :root {
            --bg0: #050712;
            --bg1: #070b18;
            --glass: rgba(255, 255, 255, .08);
            --stroke: rgba(255, 255, 255, .14);
            --text: rgba(255, 255, 255, .92);
            --muted: rgba(255, 255, 255, .68);
            --accent: #22d3ee;
            --accent2: #60a5fa;
            --gold: #ffd166;
            --good: #22c55e;
            --bad: #ef4444;
            --shadow: 0 22px 70px rgba(0, 0, 0, .55);
            --shadow2: 0 16px 42px rgba(0, 0, 0, .45);
            --r: 18px;
            --mono: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
        }

        * {
            box-sizing: border-box
        }

        html,
        body {
            height: 100%
        }

        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(1200px 680px at 18% 0%, rgba(34, 211, 238, .16), transparent 56%),
                radial-gradient(900px 520px at 86% 12%, rgba(96, 165, 250, .14), transparent 52%),
                radial-gradient(900px 700px at 50% 100%, rgba(255, 209, 102, .06), transparent 55%),
                linear-gradient(180deg, var(--bg1), var(--bg0));
            overflow: hidden;
        }

        .topbar {
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .brand {
            display: flex;
            gap: 10px;
            align-items: center;
            user-select: none
        }

        .logo {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: conic-gradient(from 210deg, rgba(34, 211, 238, .92), rgba(96, 165, 250, .85), rgba(255, 209, 102, .58), rgba(34, 211, 238, .92));
            box-shadow: 0 16px 46px rgba(34, 211, 238, .14);
            border: 1px solid rgba(255, 255, 255, .18);
        }

        .topbar-logo {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #fff;
            padding: 1px;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.8),
                0 8px 18px rgba(0, 0, 0, 0.6);
        }

        .brand h1 {
            margin: 0;
            font-size: 15px;
            letter-spacing: .35px;
            font-weight: 900
        }

        .brand .sub {
            font-size: 12px;
            color: var(--muted);
            margin-top: 2px
        }

        .chips {
            display: flex;
            gap: 10px;
            align-items: center;
            color: var(--muted);
            font-size: 12px;
            flex-wrap: wrap;
            justify-content: flex-end
        }

        .pill {
            padding: 7px 10px;
            border-radius: 999px;
            background: rgba(0, 0, 0, .34);
            border: 1px solid rgba(255, 255, 255, .12);
            backdrop-filter: blur(12px);
            box-shadow: 0 14px 40px rgba(0, 0, 0, .30);
            color: rgba(255, 255, 255, .90);
            white-space: nowrap;
        }

        .pill b {
            color: #fff
        }

        .wrap {
            height: calc(100vh - 62px);
            padding: 0 16px 16px;
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 14px;
        }

        .stage {
            position: relative;
            border-radius: var(--r);
            background: rgba(255, 255, 255, .04);
            border: 1px solid var(--stroke);
            box-shadow: var(--shadow);
            overflow: hidden;
            min-height: 520px;
        }

        canvas {
            width: 100%;
            height: 100%;
            display: block
        }

        .hud {
            position: absolute;
            left: 14px;
            top: 14px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            pointer-events: none;
            z-index: 10;
        }

        .panel {
            border-radius: var(--r);
            background: linear-gradient(180deg, rgba(255, 255, 255, .10), rgba(255, 255, 255, .06));
            border: 1px solid rgba(255, 255, 255, .15);
            box-shadow: var(--shadow);
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            overflow: auto;
        }

        .sectionTitle {
            font-size: 12px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .16em;
            margin-bottom: 6px;
        }

        .controlsRow {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 10px;
            border-radius: 16px;
            background: rgba(0, 0, 0, .18);
            border: 1px solid rgba(255, 255, 255, .12);
            box-shadow: var(--shadow2);
        }

        .btn {
            border: 1px solid rgba(255, 255, 255, .15);
            background: rgba(255, 255, 255, .08);
            color: var(--text);
            border-radius: 14px;
            padding: 10px 12px;
            font-weight: 900;
            cursor: pointer;
            transition: transform .08s ease, background .15s ease, border-color .15s ease;
            user-select: none;
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }

        .btn:hover {
            background: rgba(255, 255, 255, .12);
            border-color: rgba(255, 255, 255, .22)
        }

        .btn:active {
            transform: translateY(1px)
        }

        .btn.primary {
            background: linear-gradient(135deg, rgba(34, 211, 238, .92), rgba(96, 165, 250, .56));
            border-color: rgba(34, 211, 238, .42);
            color: #071023;
        }

        .btn.danger {
            background: linear-gradient(135deg, rgba(239, 68, 68, .94), rgba(239, 68, 68, .60));
            border-color: rgba(239, 68, 68, .42);
            color: #1b0707;
        }

        .icon {
            width: 18px;
            height: 18px;
            display: inline-block;
            opacity: .95
        }

        .grid2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px
        }

        .sliderRow {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 10px 12px;
            border-radius: 16px;
            background: rgba(0, 0, 0, .18);
            border: 1px solid rgba(255, 255, 255, .12);
            box-shadow: var(--shadow2);
        }

        .sliderRow .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: rgba(255, 255, 255, .84)
        }

        input[type="range"] {
            width: 100%;
            accent-color: rgba(34, 211, 238, .95);
        }

        .tag {
            font-family: var(--mono);
            font-size: 12px;
            padding: 3px 9px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .14);
            background: rgba(255, 255, 255, .08);
            color: rgba(255, 255, 255, .92);
            white-space: nowrap;
        }

        .card {
            border-radius: 16px;
            background: rgba(0, 0, 0, .18);
            border: 1px solid rgba(255, 255, 255, .12);
            padding: 10px 12px;
            box-shadow: var(--shadow2);
        }

        .kv {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin: 7px 0;
            font-size: 13px;
            color: rgba(255, 255, 255, .88);
        }

        .kv .k {
            color: rgba(255, 255, 255, .68)
        }

        .mono {
            font-family: var(--mono);
            font-size: 12px;
            white-space: pre-wrap;
            background: rgba(0, 0, 0, .24);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 14px;
            padding: 10px 12px;
            color: rgba(255, 255, 255, .88);
            overflow: auto;
            max-height: 230px;
        }

        .toastWrap {
            position: absolute;
            right: 14px;
            top: 14px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 30;
            pointer-events: none;
        }

        .toast {
            width: min(360px, 76vw);
            border-radius: 16px;
            padding: 10px 12px;
            background: rgba(0, 0, 0, .48);
            border: 1px solid rgba(255, 255, 255, .14);
            backdrop-filter: blur(12px);
            box-shadow: 0 16px 48px rgba(0, 0, 0, .55);
            animation: pop .18s ease-out;
        }

        @keyframes pop {
            from {
                transform: translateY(-6px);
                opacity: 0
            }

            to {
                transform: translateY(0);
                opacity: 1
            }
        }

        .toast .tTitle {
            font-weight: 950;
            font-size: 13px
        }

        .toast .tBody {
            font-size: 12px;
            color: rgba(255, 255, 255, .78);
            margin-top: 2px
        }

        @media (max-width:980px) {
            .wrap {
                grid-template-columns: 1fr
            }

            .panel {
                height: 380px
            }

            body {
                overflow: auto
            }
        }
    </style>
</head>

<body>

    <div class="topbar">
        <div class="brand">

            <img src="logo.jpg" class="topbar-logo" alt="Streelight Logo">

            <div>
                <h1>Smart Streetlight DFA Simulator by Jaymar</h1>
                <div class="sub">CS109 • DFA controls Streetlight output (ON/OFF) from Σ={DAY,NIGHT}</div>
            </div>
        </div>
        <div class="chips">
            <span class="pill"><b>Machine:</b> streetlight_dfa</span>
            <span class="pill"><b>Σ:</b> DAY, NIGHT</span>
            <span class="pill"><b>Q:</b> LIGHTS_OFF, LIGHTS_ON</span>
        </div>
    </div>

    <div class="wrap">
        <div class="stage">
            <canvas id="c"></canvas>

            <div class="hud">
                <div class="pill" id="pillEnv">ENV: <b>DAY</b></div>
                <div class="pill" id="pillState">STATE: <b>LIGHTS_OFF</b></div>
                <div class="pill" id="pillTime">t=<b>0.00s</b></div>
                <div class="pill" id="pillInput">last input: <b>—</b></div>
                <div class="pill" id="pillDelta">δ: <b>(LIGHTS_OFF, DAY) → LIGHTS_OFF</b></div>
                <div class="pill" id="pillSensor">SENSOR: <b>NO CAR</b></div>

            </div>

            <div class="toastWrap" id="toasts"></div>
        </div>

        <div class="panel">
            <div>
                <div class="sectionTitle">Controls</div>
                <div class="controlsRow">
                    <button class="btn primary" id="btnStart">
                        <span class="icon">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M5 16.5c0-1 .8-1.8 1.8-1.8h10.4c1 0 1.8.8 1.8 1.8v1.2c0 .6-.5 1.1-1.1 1.1h-.6a2 2 0 0 1-4 0H10.7a2 2 0 0 1-4 0H6.1c-.6 0-1.1-.5-1.1-1.1v-1.2Z"
                                    stroke="rgba(255,255,255,.92)" stroke-width="1.6" stroke-linejoin="round" />
                                <path d="M7.2 14.7 9 10.7c.2-.5.7-.8 1.2-.8h3.6c.5 0 1 .3 1.2.8l1.8 4"
                                    stroke="rgba(255,255,255,.92)" stroke-width="1.6" stroke-linecap="round" />
                                <path d="M8.2 17.5h.01M15.8 17.5h.01" stroke="rgba(255,255,255,.92)" stroke-width="2.8"
                                    stroke-linecap="round" />
                            </svg>
                        </span>
                        <span id="startLabel">Simulate</span>
                    </button>
                    <button class="btn" id="btnPause">
                        <span class="icon">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M8 6v12M16 6v12" stroke="rgba(255,255,255,.92)" stroke-width="2.2"
                                    stroke-linecap="round" />
                            </svg>
                        </span>
                        <span id="pauseLabel">Pause</span>
                    </button>
                    <button class="btn danger" id="btnReset">
                        <span class="icon">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M12 6a6 6 0 1 1-5.3 3.2" stroke="rgba(255,255,255,.92)" stroke-width="1.9"
                                    stroke-linecap="round" />
                                <path d="M6.7 6.8V11h4.2" stroke="rgba(255,255,255,.92)" stroke-width="1.9"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        Reset
                    </button>
                </div>
            </div>

            <div class="grid2">
                <div class="sliderRow">
                    <div class="top">
                        <div>Car Speed</div>
                        <div class="tag"><span id="speedVal">1.20</span>x</div>
                    </div>
                    <input id="speed" type="range" min="0.5" max="3.0" step="0.05" value="1.2">
                </div>
                <div class="sliderRow">
                    <div class="top">
                        <div>Cycle</div>
                        <div class="tag"><span id="cycleVal">8</span>s</div>
                    </div>
                    <input id="cycle" type="range" min="4" max="20" step="1" value="8">
                </div>
            </div>

            <div class="card">
                <div class="sectionTitle" style="margin:0 0 6px 0;">Live DFA</div>
                <div class="kv"><span class="k">ENV (Σ)</span><span id="uiEnv" class="tag">DAY</span></div>
                <div class="kv"><span class="k">STATE (Q)</span><span id="uiState" class="tag">LIGHTS_OFF</span></div>
                <div class="kv"><span class="k">last input</span><span id="uiInput" class="tag">—</span></div>
                <div class="kv"><span class="k">δ</span><span id="uiDelta" class="tag">(LIGHTS_OFF, DAY) →
                        LIGHTS_OFF</span></div>
                <div class="kv"><span class="k">t</span><span id="uiTime" class="tag">0.00s</span></div>
            </div>

            <div>
                <div class="sectionTitle">Formal DFA</div>
                <div class="mono" id="dfaBox"></div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const DFA = {
                Q: ["LIGHTS_OFF", "LIGHTS_ON"],
                Sigma: ["DAY", "NIGHT"],
                q0: "LIGHTS_OFF",
                F: ["LIGHTS_ON"],
                delta: (s, a) => {
                    if (s === "LIGHTS_OFF" && a === "NIGHT") return "LIGHTS_ON";
                    if (s === "LIGHTS_OFF" && a === "DAY") return "LIGHTS_OFF";
                    if (s === "LIGHTS_ON" && a === "DAY") return "LIGHTS_OFF";
                    if (s === "LIGHTS_ON" && a === "NIGHT") return "LIGHTS_ON";
                    return s;
                }
            };

            document.getElementById("dfaBox").textContent =
                `Q = { ${DFA.Q.join(", ")} }
Σ = { ${DFA.Sigma.join(", ")} }
q₀ = ${DFA.q0}
F (optional) = { ${DFA.F.join(", ")} }

δ:
(LIGHTS_OFF, NIGHT) → LIGHTS_ON
(LIGHTS_OFF, DAY)   → LIGHTS_OFF
(LIGHTS_ON,  DAY)   → LIGHTS_OFF
(LIGHTS_ON,  NIGHT) → LIGHTS_ON

Timer triggers input symbols (DAY/NIGHT); DFA transitions via δ(state,input).`;

            const canvas = document.getElementById("c");
            const ctx = canvas.getContext("2d");

            const btnStart = document.getElementById("btnStart");
            const btnPause = document.getElementById("btnPause");
            const btnReset = document.getElementById("btnReset");
            const startLabel = document.getElementById("startLabel");
            const pauseLabel = document.getElementById("pauseLabel");

            const speed = document.getElementById("speed");
            const cycle = document.getElementById("cycle");
            const speedVal = document.getElementById("speedVal");
            const cycleVal = document.getElementById("cycleVal");
            speed.addEventListener("input", () => speedVal.textContent = Number(speed.value).toFixed(2));
            cycle.addEventListener("input", () => cycleVal.textContent = cycle.value);

            const pillEnv = document.getElementById("pillEnv");
            const pillState = document.getElementById("pillState");
            const pillTime = document.getElementById("pillTime");
            const pillInput = document.getElementById("pillInput");
            const pillDelta = document.getElementById("pillDelta");
            const pillSensor = document.getElementById("pillSensor");

            const uiEnv = document.getElementById("uiEnv");
            const uiState = document.getElementById("uiState");
            const uiInput = document.getElementById("uiInput");
            const uiDelta = document.getElementById("uiDelta");
            const uiTime = document.getElementById("uiTime");

            const toasts = document.getElementById("toasts");
            function toast(title, body) {
                const el = document.createElement("div");
                el.className = "toast";
                el.innerHTML = `<div class="tTitle">${title}</div><div class="tBody">${body}</div>`;
                toasts.appendChild(el);
                setTimeout(() => { el.style.opacity = "0"; el.style.transform = "translateY(-6px)"; }, 2200);
                setTimeout(() => { el.remove(); }, 2550);
            }

            const mix = (a, b, m) => a * (1 - m) + b * m;
            const clamp01 = x => Math.max(0, Math.min(1, x));
            const ease = t => { t = clamp01(t); return t * t * (3 - 2 * t); };

            let running = false, paused = false, simStart = 0, lastFrame = 0;

            let env = "NIGHT";
            let state = DFA.q0;
            let lastInput = "—";

            let W = 0, H = 0;
            let roadY = 0;
            let streetlights = [];
            let stars = [];
            let flashUntil = 0;

            let carX = 0;
            let carCX = 0;

            const SENSOR = {
                rangePx: 160
            };

            function lampCarDetected(lampX) {
                if (env !== "NIGHT") return false;
                const range = SENSOR.rangePx * devicePixelRatio;
                return Math.abs(carCX - lampX) <= range;
            }

            function anyLampDetected() {
                for (const L of streetlights) {
                    if (lampCarDetected(L.x + H * 0.06)) return true;
                }
                return false;
            }

            let night = 1;
            let nightTarget = 1;

            function simTime(now) { return (now - simStart) / 1000; }

            function resize() {
                const r = canvas.getBoundingClientRect();
                canvas.width = Math.floor(r.width * devicePixelRatio);
                canvas.height = Math.floor(r.height * devicePixelRatio);
                W = canvas.width;
                H = canvas.height;
                roadY = H * 0.67;
                initStreetlights();
                initStars();
                drawFrame(0, 0);
            }
            window.addEventListener("resize", resize);

            function initStreetlights() {
                streetlights = [];
                const count = 7;
                const margin = W * 0.10;
                const gap = (W - margin * 2) / (count - 1);
                for (let i = 0; i < count; i++) {
                    streetlights.push({ x: margin + i * gap, phase: Math.random() * 10 });
                }
            }
            function initStars() {
                stars = [];
                for (let i = 0; i < 120; i++) {
                    stars.push({ x: Math.random(), y: Math.random() * 0.60, s: (Math.random() * 1.7 + 0.7) * devicePixelRatio, a: Math.random() * 0.6 + 0.25, tw: Math.random() * 2.4 + 0.6 });
                }
            }

            async function logTransition(prev, input, next, t, carPos) {
                try {
                    await fetch("log.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({
                            machine_name: "streetlight_dfa",
                            prev_state: prev,
                            input_symbol: input,
                            next_state: next,
                            sim_time: Number(t.toFixed(2)),
                            car_position: Number((carPos / devicePixelRatio).toFixed(2))
                        })
                    });
                } catch (e) { }
            }

            function updateUI(t) {
                pillEnv.innerHTML = `ENV: <b>${env}</b>`;
                pillState.innerHTML = `STATE: <b>${state}</b>`;
                pillTime.innerHTML = `t=<b>${t.toFixed(2)}s</b>`;
                pillInput.innerHTML = `last input: <b>${lastInput}</b>`;
                const next = DFA.delta(state, env);
                const d = `(${state}, ${env}) → ${next}`;
                pillDelta.innerHTML = `δ: <b>${d}</b>`;
                if (pillSensor) {
                    const detected = anyLampDetected();
                    pillSensor.innerHTML = `SENSOR: <b>${detected ? "CAR DETECTED" : "NO CAR"}</b>`;
                }

                uiEnv.textContent = env;
                uiState.textContent = state;
                uiInput.textContent = lastInput;
                uiDelta.textContent = d;
                uiTime.textContent = `${t.toFixed(2)}s`;
            }

            async function applyInput(inputSymbol, t) {
                const prev = state;
                const next = DFA.delta(state, inputSymbol);
                lastInput = inputSymbol;
                state = next;
                flashUntil = performance.now() + 520;

                if (prev !== next) {
                    await logTransition(prev, inputSymbol, next, t, carX);
                    toast("DFA Transition", `${prev} + ${inputSymbol} → ${next}`);
                } else {
                    toast("Input Applied", `${prev} + ${inputSymbol} → ${next}`);
                }
            }

            async function timedEnvSwitch(t) {
                const c = Number(cycle.value);
                const phase = t % (2 * c);
                const newEnv = (phase < c) ? "DAY" : "NIGHT";
                if (newEnv !== env) {
                    env = newEnv;
                    nightTarget = (env === "NIGHT") ? 1 : 0;
                    await applyInput(env, t);
                    toast(env === "NIGHT" ? "Night detected" : "Day detected", env === "NIGHT" ? "Output: Streetlights ON" : "Output: Streetlights OFF");
                }
            }

            function drawBackground(t, dt) {
                night = mix(night, nightTarget, 1 - Math.pow(0.001, dt));
                night = ease(night);

                const nightTop = [0, 0, 0], nightBot = [2, 5, 18];
                const dayTop = [28, 65, 120], dayBot = [175, 245, 255];
                const mt = (a, b) => Math.round(mix(a, b, 1 - night));

                const g = ctx.createLinearGradient(0, 0, 0, H);
                g.addColorStop(0, `rgb(${mt(nightTop[0], dayTop[0])},${mt(nightTop[1], dayTop[1])},${mt(nightTop[2], dayTop[2])})`);
                g.addColorStop(1, `rgb(${mt(nightBot[0], dayBot[0])},${mt(nightBot[1], dayBot[1])},${mt(nightBot[2], dayBot[2])})`);
                ctx.fillStyle = g;
                ctx.fillRect(0, 0, W, H);

                const v = ctx.createRadialGradient(W * 0.5, H * 0.45, H * 0.12, W * 0.5, H * 0.45, H * 0.95);
                v.addColorStop(0, "rgba(0,0,0,0)");
                v.addColorStop(1, `rgba(0,0,0,${0.28 + 0.20 * night})`);
                ctx.fillStyle = v;
                ctx.fillRect(0, 0, W, H);

                const orbX = W * 0.86, orbY = H * 0.16, orbR = H * 0.060;
                ctx.beginPath();
                ctx.arc(orbX, orbY, orbR, 0, Math.PI * 2);
                ctx.fillStyle = (night > 0.55) ? "rgba(230,230,230,0.92)" : "rgba(255,214,74,0.95)";
                ctx.fill();

                if (night > 0.02) {
                    ctx.fillStyle = "rgba(255,255,255,1)";
                    for (const s of stars) {
                        const tw = 0.5 + 0.5 * Math.sin(t * s.tw + s.x * 8);
                        ctx.globalAlpha = s.a * night * (0.55 + 0.45 * tw);
                        ctx.fillRect(s.x * W, s.y * H, s.s, s.s);
                    }
                    ctx.globalAlpha = 1;
                }
            }

            function drawRoad(t) {
                const roadH = H * 0.23;
                const y = roadY;

                ctx.fillStyle = "rgba(10,12,18,0.92)";
                ctx.fillRect(0, y, W, roadH);

                const rg = ctx.createLinearGradient(0, y, 0, y + roadH);
                rg.addColorStop(0, `rgba(255,255,255,${0.04 + 0.08 * night})`);
                rg.addColorStop(1, "rgba(255,255,255,0)");
                ctx.fillStyle = rg;
                ctx.fillRect(0, y, W, roadH);

                ctx.strokeStyle = "rgba(255,255,255,0.75)";
                ctx.lineWidth = 4 * devicePixelRatio;
                ctx.setLineDash([22 * devicePixelRatio, 18 * devicePixelRatio]);
                ctx.lineDashOffset = -t * 70 * devicePixelRatio;
                ctx.beginPath();
                ctx.moveTo(0, y + roadH * 0.52);
                ctx.lineTo(W, y + roadH * 0.52);
                ctx.stroke();
                ctx.setLineDash([]);
            }

            function drawStateBadge(x, y, text, on) {
                const padX = 8 * devicePixelRatio, padY = 5 * devicePixelRatio;
                ctx.font = `${12 * devicePixelRatio}px ${getComputedStyle(document.body).fontFamily}`;
                const tw = ctx.measureText(text).width;
                const w = tw + padX * 2;
                const h = 22 * devicePixelRatio;
                const flash = performance.now() < flashUntil;
                const a = flash ? 0.98 : 0.78;

                ctx.save();
                ctx.globalAlpha = a;
                ctx.fillStyle = on ? `rgba(255,209,102,0.18)` : `rgba(34,211,238,0.14)`;
                ctx.strokeStyle = on ? `rgba(255,209,102,0.44)` : `rgba(34,211,238,0.40)`;
                ctx.lineWidth = 1.4 * devicePixelRatio;
                ctx.beginPath();
                ctx.roundRect(x - w / 2, y - h / 2, w, h, 10 * devicePixelRatio);
                ctx.fill(); ctx.stroke();

                ctx.fillStyle = on ? `rgba(255,244,180,0.95)` : `rgba(220,245,255,0.92)`;
                ctx.textAlign = "center";
                ctx.textBaseline = "middle";
                ctx.fillText(text, x, y + 0.5 * devicePixelRatio);
                ctx.restore();
            }

            function drawStreetlights(t) {
                const poleH = H * 0.26;
                const baseY = roadY + 2 * devicePixelRatio;
                const lampR = H * 0.016;

                const dfaLightsOn = (state === "LIGHTS_ON" && env === "NIGHT");

                for (const L of streetlights) {
                    const x = L.x;

                    ctx.strokeStyle = "rgba(160,170,190,0.55)";
                    ctx.lineWidth = 6 * devicePixelRatio;
                    ctx.beginPath();
                    ctx.moveTo(x, baseY);
                    ctx.lineTo(x, baseY - poleH);
                    ctx.stroke();

                    ctx.lineWidth = 4 * devicePixelRatio;
                    ctx.beginPath();
                    ctx.moveTo(x, baseY - poleH);
                    ctx.lineTo(x + H * 0.06, baseY - poleH + H * 0.02);
                    ctx.stroke();

                    const lx = x + H * 0.06;
                    const ly = baseY - poleH + H * 0.02;

                    ctx.fillStyle = "rgba(20,24,32,0.86)";
                    ctx.fillRect(lx - lampR * 1.4, ly - lampR * 0.9, lampR * 2.8, lampR * 1.8);

                    const detectedHere = dfaLightsOn ? lampCarDetected(lx) : false;
                    const intensity = dfaLightsOn ? (detectedHere ? 1.0 : 0.50) : 0.0;

                    if (dfaLightsOn) {
                        ctx.save();
                        ctx.globalCompositeOperation = "lighter";

                        const flick = 0.90 + 0.10 * Math.sin(t * 2.2 + L.phase);
                        ctx.globalAlpha = (0.06 + 0.12 * night) * flick * intensity;

                        ctx.beginPath();
                        ctx.moveTo(lx, ly + lampR * 0.8);
                        ctx.lineTo(lx - lampR * 9.2, ly + lampR * 16.2);
                        ctx.lineTo(lx + lampR * 9.2, ly + lampR * 16.2);
                        ctx.closePath();
                        ctx.fillStyle = "rgba(255,245,160,1)";
                        ctx.fill();

                        const glow = ctx.createRadialGradient(lx, ly, 0, lx, ly, lampR * 6.8);
                        glow.addColorStop(0, `rgba(255,250,205,${0.22 * intensity})`);
                        glow.addColorStop(1, "rgba(255,250,205,0)");
                        ctx.fillStyle = glow;
                        ctx.beginPath();
                        ctx.arc(lx, ly, lampR * 6.8, 0, Math.PI * 2);
                        ctx.fill();

                        ctx.restore();
                    }

                    ctx.beginPath();
                    ctx.arc(lx, ly, lampR, 0, Math.PI * 2);
                    ctx.fillStyle = dfaLightsOn
                        ? `rgba(255,234,163,${0.55 + 0.45 * intensity})`
                        : "rgba(140,150,165,0.85)";
                    ctx.fill();
                    ctx.strokeStyle = "rgba(0,0,0,0.35)";
                    ctx.lineWidth = 2 * devicePixelRatio;
                    ctx.stroke();

                    drawStateBadge(
                        lx,
                        ly - 26 * devicePixelRatio,
                        dfaLightsOn ? (detectedHere ? "FULL" : "DIM") : "OFF",
                        dfaLightsOn
                    );
                }
            }


            function wheel(x, y, r) {
                ctx.fillStyle = "#0b0d12";
                ctx.beginPath(); ctx.arc(x, y, r, 0, Math.PI * 2); ctx.fill();
                ctx.strokeStyle = "rgba(255,255,255,0.12)";
                ctx.lineWidth = 2 * devicePixelRatio; ctx.stroke();
                ctx.beginPath(); ctx.arc(x, y, r * 0.45, 0, Math.PI * 2);
                ctx.strokeStyle = "rgba(255,255,255,0.16)"; ctx.stroke();
            }

            function drawCar(dt, t) {
                const roadH = H * 0.23;
                const y = roadY + roadH * 0.64;

                const sp = Number(speed.value);
                carX += dt * sp * 240 * devicePixelRatio;
                if (carX > W + 260 * devicePixelRatio) carX = -280 * devicePixelRatio;

                const bw = 170 * devicePixelRatio;
                const bh = 46 * devicePixelRatio;
                carCX = carX + bw * 0.55;

                ctx.globalAlpha = 0.18;
                ctx.fillStyle = "rgba(0,0,0,0.9)";
                ctx.fillRect(carX - 26 * devicePixelRatio, y + 11 * devicePixelRatio, bw + 52 * devicePixelRatio, 8 * devicePixelRatio);
                ctx.globalAlpha = 1;

                const bodyG = ctx.createLinearGradient(carX, y - bh, carX + bw, y);
                bodyG.addColorStop(0, "#2dd4bf");
                bodyG.addColorStop(0.55, "#3b82f6");
                bodyG.addColorStop(1, "#1d4ed8");
                ctx.fillStyle = bodyG;
                ctx.beginPath();
                ctx.roundRect(carX, y - bh, bw, bh, 12 * devicePixelRatio);
                ctx.fill();

                ctx.fillStyle = "rgba(255,255,255,0.10)";
                ctx.beginPath();
                ctx.roundRect(carX + 8 * devicePixelRatio, y - bh + 8 * devicePixelRatio, bw - 16 * devicePixelRatio, bh - 16 * devicePixelRatio, 10 * devicePixelRatio);
                ctx.fill();

                ctx.fillStyle = "rgba(15,18,26,0.52)";
                ctx.beginPath();
                ctx.roundRect(carX + 30 * devicePixelRatio, y - bh - 24 * devicePixelRatio, 92 * devicePixelRatio, 24 * devicePixelRatio, 10 * devicePixelRatio);
                ctx.fill();

                ctx.fillStyle = "rgba(210,240,255,0.44)";
                ctx.beginPath();
                ctx.roundRect(carX + 42 * devicePixelRatio, y - bh - 18 * devicePixelRatio, 30 * devicePixelRatio, 14 * devicePixelRatio, 6 * devicePixelRatio);
                ctx.fill();
                ctx.beginPath();
                ctx.roundRect(carX + 76 * devicePixelRatio, y - bh - 18 * devicePixelRatio, 34 * devicePixelRatio, 14 * devicePixelRatio, 6 * devicePixelRatio);
                ctx.fill();

                wheel(carX + 40 * devicePixelRatio, y, 12 * devicePixelRatio);
                wheel(carX + 130 * devicePixelRatio, y, 12 * devicePixelRatio);

                if (env === "NIGHT") {
                    ctx.save();
                    ctx.globalCompositeOperation = "lighter";
                    ctx.globalAlpha = 0.10 + 0.10 * night;
                    ctx.beginPath();
                    ctx.moveTo(carX + bw, y - bh + 12 * devicePixelRatio);
                    ctx.lineTo(carX + bw + 170 * devicePixelRatio, y - bh - 46 * devicePixelRatio);
                    ctx.lineTo(carX + bw + 170 * devicePixelRatio, y - bh + 70 * devicePixelRatio);
                    ctx.closePath();
                    ctx.fillStyle = "rgba(255,255,220,1)";
                    ctx.fill();
                    ctx.restore();
                }
            }

            function drawFrame(dt, t) {
                drawBackground(t, dt);
                drawRoad(t);
                drawStreetlights(t);
                drawCar(dt, t);
                updateUI(t);
            }

            async function tick(now) {
                if (!running) return;

                if (!lastFrame) lastFrame = now;
                const dt = (now - lastFrame) / 1000;
                lastFrame = now;

                const t = simTime(now);

                if (!paused) await timedEnvSwitch(t);

                drawFrame(dt, t);
                requestAnimationFrame(tick);
            }

            function resetAll() {
                running = false;
                paused = false;
                simStart = 0;
                lastFrame = 0;

                env = "NIGHT";
                state = DFA.q0;
                lastInput = "—";
                carX = -280 * devicePixelRatio;

                night = 1;
                nightTarget = 1;

                startLabel.textContent = "Simulate";
                pauseLabel.textContent = "Pause";
                toast("Reset", "Starts at NIGHT (black background). q₀ = LIGHTS_OFF.");
                drawFrame(0, 0);
            }

            btnStart.addEventListener("click", () => {
                if (!running) {
                    running = true;
                    paused = false;
                    simStart = performance.now();
                    lastFrame = 0;
                    startLabel.textContent = "Running";
                    toast("Started", "Smooth fade day↔night enabled.");
                    requestAnimationFrame(tick);
                    return;
                }
                paused = false;
                pauseLabel.textContent = "Pause";
                toast("Running", "Simulation continues.");
            });

            btnPause.addEventListener("click", () => {
                if (!running) { toast("Not running", "Click Simulate first."); return; }
                paused = !paused;
                pauseLabel.textContent = paused ? "Resume" : "Pause";
                toast(paused ? "Paused" : "Resumed", paused ? "Paused (state preserved)." : "Resumed.");
            });

            btnReset.addEventListener("click", resetAll);

            resize();
            resetAll();
        })();
    </script>

</body>

</html>