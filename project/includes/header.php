<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $page_title ?? 'Dashboard' ?> · Pulse Attendance</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<script>
tailwind.config = {
    theme: { extend: {
        fontFamily: {
            display: ['Sora','sans-serif'],
            body: ['Inter','sans-serif'],
            mono: ['JetBrains Mono','monospace'],
        },
        colors: {
            ink:    '#0B2027',
            teal:   { 50:'#EAF6F4',100:'#CFEAE5',300:'#7FC4B8',500:'#1F7A6C',600:'#176358',700:'#114A42',900:'#0B2027' },
            amber:  { 100:'#FFF1D6',300:'#FFD27D',500:'#F2A93B',600:'#DB8A1B' },
            coral:  '#E8604C',
            sand:   '#FBF8F3',
        }
    }}
}
</script>
<style>
*{font-family:'Inter',sans-serif}
.font-display{font-family:'Sora',sans-serif}
.font-mono{font-family:'JetBrains Mono',monospace}
body{background:#FBF8F3}

/* Sidebar nav links */
.nav-link{position:relative;transition:all .18s ease;color:rgba(255,255,255,.62)}
.nav-link i{transition:transform .18s ease}
.nav-link:hover{color:#fff;background:rgba(255,255,255,.06)}
.nav-link.active{color:#0B2027;background:#F2A93B;font-weight:600}
.nav-link.active i{color:#0B2027}

/* Cards */
.panel{background:#fff;border:1px solid #ECE6D9;border-radius:18px;box-shadow:0 1px 2px rgba(11,32,39,.04)}
.panel-row:hover{background:#FBF8F3}

/* Status chips */
.chip{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;padding:4px 10px;border-radius:999px;letter-spacing:.02em}
.chip-present{background:#EAF6F4;color:#176358}
.chip-late{background:#FFF1D6;color:#DB8A1B}
.chip-absent{background:#FBE9E6;color:#E8604C}

/* Number ticker font */
.stat-num{font-family:'Sora',sans-serif;font-weight:700;letter-spacing:-.02em}

::-webkit-scrollbar{width:6px;height:6px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:#CFEAE5;border-radius:3px}

@keyframes riseIn{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
.rise-in{animation:riseIn .45s cubic-bezier(.16,1,.3,1) forwards}
@keyframes pulseDot{0%,100%{opacity:1}50%{opacity:.35}}
.pulse-dot{animation:pulseDot 1.8s ease-in-out infinite}
</style>
</head>
<body class="text-ink">
