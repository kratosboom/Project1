/**
 * laju22.net — salin daftar game ke format JSON impor (jalankan di browser)
 *
 * 1. Buka https://laju22.net/slots/pragmatic-play (tunggu halaman selesai).
 * 2. F12 → tab Console, tempel skrip ini, Enter.
 * 3. JSON tercetak; salin ke Admin → Game → Impor. Atau: `copy( ... )` jika disediakan.
 *
 * Bila jumlah 0, sesuaikan SELECTOR bawah, inspect elemen grid game di halaman Anda.
 */
(function () {
  const DEFAULT_PROVIDER = "pragmatic";
  // Sesuaikan jika perlu: contoh '.game-card, [data-name], article a'
  const ROOT_SELECTOR = [
    ".game-card",
    "[data-name]",
    "a[href*='/game/']",
    "a[href*='/slots/']",
  ].join(", ");

  const seen = new Set();
  const games = [];

  function add(name, source, provider) {
    if (!name || name.length < 2) return;
    const n = name.trim();
    if (/^logo$|^hot$|^best$/i.test(n)) return;
    const k = n.toLowerCase() + (source || "");
    if (seen.has(k)) return;
    seen.add(k);
    games.push({ name: n, provider: provider || DEFAULT_PROVIDER, source: source || null });
  }

  document.querySelectorAll(ROOT_SELECTOR).forEach((el) => {
    const name =
      el.getAttribute("data-name") ||
      el.getAttribute("data-title") ||
      el.getAttribute("title") ||
      (el.querySelector("img[alt]") && el.querySelector("img[alt]").getAttribute("alt"));
    const img = el.querySelector("img");
    const src = img && img.getAttribute("src");
    if (name) add(name, src, el.getAttribute("data-provider") || DEFAULT_PROVIDER);
  });

  if (games.length < 2) {
    // fallback: semua img dengan alt panjang (bukan dekor)
    document.querySelectorAll("img[alt][src]").forEach((img) => {
      const n = (img.getAttribute("alt") || "").trim();
      if (n.length < 3 || n.length > 120) return;
      if (/icon|logo|banner|avatar|decoration|spinner/i.test(n)) return;
      add(n, img.getAttribute("src"), DEFAULT_PROVIDER);
    });
  }

  const json = JSON.stringify({ games }, null, 2);
  console.log(json);
  try {
    copy(json);
    console.log("Tersalin ke clipboard:", games.length, "game");
  } catch (e) {
    console.log("Browser tidak support copy(); salin manual dari log di atas.");
  }
  return { games };
})();
