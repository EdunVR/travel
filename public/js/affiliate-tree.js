/**
 * AffiliateTree — SVG pohon jenjang MLM interaktif
 *
 * Strategi layout:
 * - Semua koordinat dalam ruang virtual 1000×600 (atau 1000×H proporsional)
 * - SVG pakai viewBox + preserveAspectRatio="xMidYMid meet" → auto-fit ke container
 * - Container pakai aspect-ratio CSS → tidak overflow, tidak scroll
 * - Node radius tetap kecil (24px dalam ruang virtual), terlihat proporsional
 */
class AffiliateTree {
    constructor(svgEl, nodes, edges, options = {}) {
        this.svg   = svgEl;
        this.nodes = nodes;
        this.edges = edges;
        this.opts  = Object.assign({
            isAdmin:    true,
            csrfToken:  '',
            feeUrl:     '',
            viewBase:   '',
            updateBase: '',
        }, options);

        this.positions  = {};
        this.nodeEls    = {};
        this.edgeEls    = [];
        this.activeNode = null;
        this.activeEdge = null;
        this._popup     = null;
        this._viewBox   = { ox: 0, oy: 0, vw: 1000, vh: 600 };

        this._createPopup();
    }

    // ─── Public ───────────────────────────────────────────────────────────────
    render() {
        this._layout();
        this._draw();
        this._setupResize();
    }

    // ─── Layout dalam ruang virtual ───────────────────────────────────────────
    _layout() {
        const nodes = this.nodes;
        const edges = this.edges;

        if (!nodes.length) return;

        // Build children map (edge.to = parent, edge.from = child)
        const childrenOf = {};
        nodes.forEach(n => { childrenOf[n.id] = []; });
        edges.forEach(e => {
            if (childrenOf[e.to] !== undefined) childrenOf[e.to].push(e.from);
        });

        // Roots = nodes with no parent
        const hasParent = new Set(edges.map(e => e.from));
        let roots = nodes.filter(n => !hasParent.has(n.id));
        if (!roots.length) roots = [nodes[0]];

        // Hitung lebar subtree (jumlah leaf)
        const leafCount = {};
        const calcLeaves = (id) => {
            const ch = childrenOf[id] || [];
            if (!ch.length) { leafCount[id] = 1; return 1; }
            const w = ch.reduce((s, c) => s + calcLeaves(c), 0);
            leafCount[id] = w;
            return w;
        };
        roots.forEach(r => calcLeaves(r.id));

        // Hitung kedalaman
        const depthOf = {};
        const calcDepth = (id, d) => {
            depthOf[id] = d;
            (childrenOf[id] || []).forEach(c => calcDepth(c, d + 1));
        };
        roots.forEach(r => calcDepth(r.id, 0));

        const totalLeaves = Math.max(roots.reduce((s, r) => s + (leafCount[r.id] || 1), 0), 1);
        const maxDepth    = Math.max(...Object.values(depthOf), 0);
        const totalRows   = maxDepth + 1;

        // Ruang virtual: lebar 1000, tinggi proporsional
        // Paksa minimum 6 kolom dan 3 baris agar node tidak terlalu besar saat data sedikit
        const MIN_COLS = 6;
        const MIN_ROWS = 3;
        const effectiveCols = Math.max(totalLeaves, MIN_COLS);
        const effectiveRows = Math.max(totalRows,   MIN_ROWS);

        const VW   = 1000;
        const VH   = Math.max(300, effectiveRows * 180);
        const COLW = VW / effectiveCols;
        const ROWH = VH / effectiveRows;

        // Assign posisi — node diletakkan di tengah ruang virtual
        // Gunakan COLW asli (berdasarkan totalLeaves) untuk spacing, tapi offset ke tengah
        const realCOLW = VW / Math.max(totalLeaves, 1);
        const startX   = (VW - realCOLW * totalLeaves) / 2 + realCOLW / 2;
        let xCursor    = startX;

        const assignPos = (id, depth) => {
            const ch = childrenOf[id] || [];
            if (!ch.length) {
                this.positions[id] = { x: xCursor, y: depth * ROWH + ROWH / 2 };
                xCursor += realCOLW;
                return this.positions[id].x;
            }
            const xs = ch.map(c => assignPos(c, depth + 1));
            const cx = (Math.min(...xs) + Math.max(...xs)) / 2;
            this.positions[id] = { x: cx, y: depth * ROWH + ROWH / 2 };
            return cx;
        };
        roots.forEach(r => assignPos(r.id, 0));

        // Nodes tanpa edge — letakkan di tengah
        nodes.forEach(n => {
            if (!this.positions[n.id]) {
                this.positions[n.id] = { x: xCursor, y: VH / 2 };
                xCursor += realCOLW;
            }
        });

        // Padding kecil
        const pad = 40;
        this._viewBox = { ox: -pad, oy: -pad, vw: VW + pad * 2, vh: VH + pad * 2 };
        // nodeR dibatasi oleh slot efektif (bukan slot aktual) agar tidak terlalu besar
        this._nodeR   = Math.min(28, Math.max(12, Math.min(COLW, ROWH) * 0.26));

        this.svg.setAttribute('viewBox', `${-pad} ${-pad} ${VW + pad * 2} ${VH + pad * 2}`);
        this.svg.setAttribute('preserveAspectRatio', 'xMidYMid meet');

        // Set container aspect ratio agar tidak overflow
        const ratio = (VW + pad * 2) / (VH + pad * 2);
        const parent = this.svg.parentElement;
        if (parent) {
            // Gunakan aspect-ratio CSS — container tingginya mengikuti lebar
            parent.style.aspectRatio = `${Math.round(ratio * 100) / 100}`;
            parent.style.height      = 'auto';
            parent.style.maxHeight   = '72vh';
            // SVG ikut tinggi container
            this.svg.style.height = '100%';
        }
    }

    // ─── Draw ─────────────────────────────────────────────────────────────────
    _draw() {
        this.svg.innerHTML = '';
        this.nodeEls = {};
        this.edgeEls = [];

        const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        this.svg.appendChild(g);

        this.edges.forEach(e => this._drawEdge(g, e));
        this.nodes.forEach(n => this._drawNode(g, n));

        this.svg.addEventListener('click', (ev) => {
            if (ev.target === this.svg || ev.target.tagName === 'svg') this._hidePopup();
        });
    }

    _drawEdge(g, edge) {
        const from = this.positions[edge.from];
        const to   = this.positions[edge.to];
        if (!from || !to) return;

        const hasFee = edge.has_fee;
        const pct    = edge.percentage || 0;
        const R      = this._nodeR;

        const dx = to.x - from.x, dy = to.y - from.y;
        const dist = Math.sqrt(dx * dx + dy * dy) || 1;
        const fx = from.x + (dx / dist) * R;
        const fy = from.y + (dy / dist) * R;
        const tx = to.x   - (dx / dist) * R;
        const ty = to.y   - (dy / dist) * R;
        const my = (fy + ty) / 2;
        const d  = `M ${fx} ${fy} C ${fx} ${my}, ${tx} ${my}, ${tx} ${ty}`;

        // Hit area
        const hit = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        hit.setAttribute('d', d);
        hit.setAttribute('fill', 'none');
        hit.setAttribute('stroke', 'transparent');
        hit.setAttribute('stroke-width', '16');
        hit.style.cursor = 'pointer';
        g.appendChild(hit);

        // Visible line
        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        path.setAttribute('d', d);
        path.setAttribute('fill', 'none');
        path.setAttribute('stroke', hasFee ? '#16a34a' : '#94a3b8');
        path.setAttribute('stroke-width', hasFee ? '2' : '1.5');
        if (!hasFee) path.setAttribute('stroke-dasharray', '5,4');
        path.style.transition = 'stroke 0.15s, stroke-width 0.15s';
        g.appendChild(path);

        // Label — tampilkan jika ada setting fee ATAU ada fee aktual
        const lx = (fx + tx) / 2;
        const ly = (fy + ty) / 2 - 6;
        const fs = Math.max(7, this._nodeR * 0.38);

        const feeType  = edge.fee_type  || 'percentage';
        const feeValue = parseFloat(edge.fee_value) || parseFloat(edge.percentage) || 0;
        const hasLabel = feeValue > 0 || (hasFee && edge.fee_total > 0);

        if (hasLabel) {
            const lines = [];
            if (feeValue > 0) {
                lines.push(feeType === 'flat'
                    ? `Rp ${this._fmt(feeValue)}`
                    : `${feeValue}%`);
            }
            if (hasFee && edge.fee_total > 0) lines.push(`Σ Rp ${this._fmt(edge.fee_total)}`);
            const bw = Math.max(fs * 7, 50), bh = lines.length * (fs + 3) + 6;
            const bg = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
            bg.setAttribute('x', lx - bw / 2); bg.setAttribute('y', ly - fs - 3);
            bg.setAttribute('width', bw); bg.setAttribute('height', bh);
            bg.setAttribute('rx', '5');
            bg.setAttribute('fill', hasFee ? '#dcfce7' : '#f1f5f9');
            bg.setAttribute('stroke', hasFee ? '#86efac' : '#e2e8f0');
            bg.setAttribute('stroke-width', '0.8');
            bg.style.pointerEvents = 'none';
            g.appendChild(bg);
            lines.forEach((line, i) => {
                const t = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                t.setAttribute('x', lx);
                t.setAttribute('y', ly + i * (fs + 3));
                t.setAttribute('text-anchor', 'middle');
                t.setAttribute('font-size', fs);
                t.setAttribute('font-weight', i === 0 ? '700' : '600');
                t.setAttribute('fill', hasFee ? '#15803d' : '#64748b');
                t.style.pointerEvents = 'none';
                t.textContent = line;
                g.appendChild(t);
            });
        }

        this.edgeEls.push({ edge, path, hit });

        hit.addEventListener('mouseenter', () => {
            path.setAttribute('stroke', hasFee ? '#15803d' : '#475569');
            path.setAttribute('stroke-width', '3.5');
        });
        hit.addEventListener('mouseleave', () => {
            if (this.activeEdge !== edge) {
                path.setAttribute('stroke', hasFee ? '#16a34a' : '#94a3b8');
                path.setAttribute('stroke-width', hasFee ? '2' : '1.5');
            }
        });
        hit.addEventListener('click', (ev) => {
            ev.stopPropagation();
            this._onEdgeClick(ev, edge, path, lx, ly);
        });
    }

    _drawNode(g, node) {
        const pos = this.positions[node.id];
        if (!pos) return;
        const R  = this._nodeR + (node.isSelf ? 3 : 0);
        const c  = this._color(node.slug);
        const fs = Math.max(7, R * 0.34);
        const fsSm = Math.max(6, R * 0.28);

        const grp = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        grp.style.cursor = 'pointer';

        // Shadow
        const sh = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        sh.setAttribute('cx', pos.x + 1.5); sh.setAttribute('cy', pos.y + 2);
        sh.setAttribute('r', R); sh.setAttribute('fill', 'rgba(0,0,0,0.10)');
        grp.appendChild(sh);

        // Circle
        const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        circle.setAttribute('cx', pos.x); circle.setAttribute('cy', pos.y);
        circle.setAttribute('r', R);
        circle.setAttribute('fill', c.fill);
        circle.setAttribute('stroke', node.isSelf ? '#fbbf24' : '#fff');
        circle.setAttribute('stroke-width', node.isSelf ? '2.5' : '2');
        circle.style.transition = 'r 0.12s';
        grp.appendChild(circle);

        // Name (max 2 lines)
        const words = node.name.split(' ');
        const l1 = words.slice(0, 2).join(' ');
        const l2 = words.slice(2).join(' ');
        const nt = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        nt.setAttribute('x', pos.x);
        nt.setAttribute('y', pos.y - (l2 ? fs * 0.6 : 1));
        nt.setAttribute('text-anchor', 'middle');
        nt.setAttribute('font-size', fs);
        nt.setAttribute('font-weight', '700');
        nt.setAttribute('fill', '#fff');
        nt.style.pointerEvents = 'none';
        const ts1 = document.createElementNS('http://www.w3.org/2000/svg', 'tspan');
        ts1.setAttribute('x', pos.x); ts1.setAttribute('dy', '0'); ts1.textContent = l1;
        nt.appendChild(ts1);
        if (l2) {
            const ts2 = document.createElementNS('http://www.w3.org/2000/svg', 'tspan');
            ts2.setAttribute('x', pos.x); ts2.setAttribute('dy', fs * 1.2); ts2.textContent = l2;
            nt.appendChild(ts2);
        }
        grp.appendChild(nt);

        // Badge
        const bw = R * 2.2, bh = fsSm + 5;
        const by = pos.y + R + bh * 0.7;
        const bb = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        bb.setAttribute('x', pos.x - bw / 2); bb.setAttribute('y', by - bh * 0.85);
        bb.setAttribute('width', bw); bb.setAttribute('height', bh);
        bb.setAttribute('rx', bh / 2); bb.setAttribute('fill', c.light);
        bb.setAttribute('stroke', c.fill); bb.setAttribute('stroke-width', '0.8');
        grp.appendChild(bb);
        const bt = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        bt.setAttribute('x', pos.x); bt.setAttribute('y', by + fsSm * 0.15);
        bt.setAttribute('text-anchor', 'middle');
        bt.setAttribute('font-size', fsSm);
        bt.setAttribute('font-weight', '700');
        bt.setAttribute('fill', c.fill);
        bt.style.pointerEvents = 'none';
        bt.textContent = node.program;
        grp.appendChild(bt);

        // Self label
        if (node.isSelf) {
            const st = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            st.setAttribute('x', pos.x); st.setAttribute('y', pos.y - R - fsSm);
            st.setAttribute('text-anchor', 'middle');
            st.setAttribute('font-size', fsSm + 1);
            st.setAttribute('fill', '#fbbf24');
            st.setAttribute('font-weight', '800');
            st.style.pointerEvents = 'none';
            st.textContent = '★ ' + (this.opts.isAdmin ? 'Mitra Ini' : 'Saya');
            grp.appendChild(st);
        }

        // Hover
        grp.addEventListener('mouseenter', () => {
            circle.setAttribute('r', R + 3);
            sh.setAttribute('r', R + 3);
            circle.setAttribute('filter', 'brightness(1.15)');
        });
        grp.addEventListener('mouseleave', () => {
            if (this.activeNode !== node) {
                circle.setAttribute('r', R);
                sh.setAttribute('r', R);
                circle.setAttribute('filter', '');
            }
        });
        grp.addEventListener('click', (ev) => {
            ev.stopPropagation();
            this._onNodeClick(ev, node, pos, R, circle, sh);
        });

        this.nodeEls[node.id] = { grp, circle, sh, R };
        g.appendChild(grp);
    }

    // ─── Interactions ─────────────────────────────────────────────────────────
    _onNodeClick(ev, node, pos, R, circle, sh) {
        if (this.activeNode && this.nodeEls[this.activeNode.id]) {
            const p = this.nodeEls[this.activeNode.id];
            p.circle.setAttribute('r', p.R); p.sh.setAttribute('r', p.R);
            p.circle.setAttribute('filter', '');
        }
        this.activeNode = node;
        circle.setAttribute('r', R + 3); sh.setAttribute('r', R + 3);
        circle.setAttribute('filter', 'brightness(1.2)');

        const svgRect = this.svg.getBoundingClientRect();
        const vb = this._viewBox;
        const scaleX = svgRect.width  / vb.vw;
        const scaleY = svgRect.height / vb.vh;
        const screenX = svgRect.left + (pos.x - vb.ox) * scaleX;
        const screenY = svgRect.top  + (pos.y - vb.oy) * scaleY;

        this._showNodePopup(node, screenX, screenY - (R + 6) * scaleY);
    }

    _onEdgeClick(ev, edge, path, lx, ly) {
        this.activeEdge = edge;
        path.setAttribute('stroke', edge.has_fee ? '#15803d' : '#475569');
        path.setAttribute('stroke-width', '3.5');

        const svgRect = this.svg.getBoundingClientRect();
        const vb = this._viewBox;
        const scaleX = svgRect.width  / vb.vw;
        const scaleY = svgRect.height / vb.vh;
        const screenX = svgRect.left + (lx - vb.ox) * scaleX;
        const screenY = svgRect.top  + (ly - vb.oy) * scaleY;

        this._showEdgePopup(edge, screenX, screenY);
    }

    // ─── Popup ────────────────────────────────────────────────────────────────
    _createPopup() {
        // Reuse existing popup if already created
        let el = document.getElementById('aff-tree-popup');
        if (!el) {
            el = document.createElement('div');
            el.id = 'aff-tree-popup';
            el.style.cssText = `
                position:fixed;z-index:9999;display:none;
                background:#fff;border-radius:14px;
                box-shadow:0 8px 32px rgba(0,0,0,0.18),0 2px 8px rgba(0,0,0,0.10);
                border:1px solid #e2e8f0;min-width:190px;
                transform:translateX(-50%) translateY(-100%);
                margin-top:-10px;
            `;
            document.head.insertAdjacentHTML('beforeend', `<style>
            @keyframes _affPopIn{from{opacity:0;transform:translateX(-50%) translateY(calc(-100% - 6px))}to{opacity:1;transform:translateX(-50%) translateY(-100%)}}
            #aff-tree-popup{animation:_affPopIn 0.13s ease}
            .aff-pb{display:flex;align-items:center;gap:9px;padding:8px 14px;font-size:12.5px;color:#334155;cursor:pointer;border:none;background:none;width:100%;text-align:left;transition:background 0.1s}
            .aff-pb:hover{background:#f8fafc}
            .aff-pb.danger{color:#dc2626}.aff-pb.danger:hover{background:#fef2f2}
            .aff-sep{height:1px;background:#f1f5f9;margin:2px 0}
            </style>`);
            document.body.appendChild(el);

            // Klik di luar popup → tutup. Klik di dalam popup → jangan tutup.
            document.addEventListener('click', (ev) => {
                if (!el.contains(ev.target)) this._hidePopup();
            });

            // Klik di dalam popup jangan bubble ke document
            el.addEventListener('click', (ev) => ev.stopPropagation());
        }
        this._popup = el;
    }

    _showNodePopup(node, sx, sy) {
        const el = this._popup;
        let html = `<div style="padding:9px 14px 7px;border-bottom:1px solid #f1f5f9">
            <div style="font-weight:700;font-size:12.5px;color:#1e293b">${node.name}</div>
            <div style="font-size:11px;color:#94a3b8;margin-top:1px">${node.program}</div>
        </div>`;
        if (this.opts.isAdmin) {
            html += `
            <button class="aff-pb" onclick="window._affTree._goView(${node.id})"><span style="color:#3b82f6">👁</span> Lihat Detail</button>
            <button class="aff-pb" onclick="window._affTree._openEditModal(${node.id})"><span style="color:#f59e0b">✏️</span> Edit Jenjang</button>
            <button class="aff-pb" onclick="window._affTree._openAddModal(${node.id})"><span style="color:#22c55e">➕</span> Tambah Downline</button>
            <div class="aff-sep"></div>
            <button class="aff-pb danger" onclick="window._affTree._confirmDelete(${node.id})"><span>🔗</span> Hapus dari Jenjang</button>`;
        }
        el.innerHTML = html;
        el.style.display = 'block';
        el.style.left = sx + 'px';
        el.style.top  = sy + 'px';
        this._currentNode = node;
        window._affTree = this;
    }

    _showEdgePopup(edge, sx, sy) {
        if (!this.opts.isAdmin) return;
        const el = this._popup;
        const fn = this.nodes.find(n => n.id === edge.from);
        const tn = this.nodes.find(n => n.id === edge.to);
        const isFlat = (edge.fee_type === 'flat');
        const curVal = isFlat ? (edge.fee_value || 0) : (edge.percentage || 0);
        const isSpecific = edge.is_specific || false;

        // Jika from_level kosong, coba ambil dari node
        const fromLevel = edge.from_level || fn?.slug || '';
        const toLevel   = edge.to_level   || tn?.slug || '';

        const levelLabel = (s) => s.replace('hm-', 'HM ').replace('-', ' ');
        const levelInfo  = (fromLevel && toLevel)
            ? `<div style="font-size:10px;color:#94a3b8;margin-top:2px">${levelLabel(fromLevel)} → ${levelLabel(toLevel)}</div>`
            : `<div style="font-size:10px;color:#ef4444;margin-top:2px">⚠ Level tidak terdeteksi — pastikan mitra memiliki program</div>`;
        
        const specificBadge = isSpecific
            ? `<div style="display:inline-block;font-size:9px;padding:2px 6px;background:#fef3c7;color:#92400e;border-radius:4px;margin-top:3px;font-weight:600">⭐ Setting Spesifik</div>`
            : `<div style="display:inline-block;font-size:9px;padding:2px 6px;background:#f1f5f9;color:#64748b;border-radius:4px;margin-top:3px;font-weight:600">🌐 Setting Global</div>`;

        el.innerHTML = `
        <div style="padding:9px 14px 7px;border-bottom:1px solid #f1f5f9">
            <div style="font-weight:700;font-size:12px;color:#1e293b">Setting Fee Jenjang</div>
            <div style="font-size:11px;color:#64748b;margin-top:1px">${fn?.name ?? '?'} → ${tn?.name ?? '?'}</div>
            ${levelInfo}
            ${specificBadge}
            ${edge.has_fee ? `<div style="font-size:10px;color:#16a34a;margin-top:2px">Fee terdistribusi: Rp ${this._fmt(edge.fee_total)}</div>` : ''}
        </div>
        <div style="padding:10px 14px">
            <div style="display:flex;gap:6px;margin-bottom:8px">
                <button id="aff-fee-btn-pct" onclick="window._affTree._switchFeeType('percentage')"
                    style="flex:1;height:28px;border-radius:6px;border:1px solid #e2e8f0;font-size:11px;font-weight:600;cursor:pointer;
                    background:${!isFlat ? '#4f46e5' : '#f8fafc'};color:${!isFlat ? '#fff' : '#64748b'}">
                    % Persen
                </button>
                <button id="aff-fee-btn-flat" onclick="window._affTree._switchFeeType('flat')"
                    style="flex:1;height:28px;border-radius:6px;border:1px solid #e2e8f0;font-size:11px;font-weight:600;cursor:pointer;
                    background:${isFlat ? '#16a34a' : '#f8fafc'};color:${isFlat ? '#fff' : '#64748b'}">
                    Rp Nominal
                </button>
            </div>
            <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:5px" id="aff-fee-label">
                ${isFlat ? 'Nominal Fee (Rp)' : 'Persentase Fee (%)'}
            </label>
            <div style="display:flex;gap:7px;align-items:center">
                <input type="number" id="aff-fee-pct" value="${curVal}" min="0" ${isFlat ? '' : 'max="100"'} step="${isFlat ? '1000' : '0.5'}"
                    style="flex:1;height:32px;padding:0 8px;border:1px solid #e2e8f0;border-radius:7px;font-size:12px;outline:none">
                <button onclick="window._affTree._saveFee()" id="aff-fee-save-btn"
                    style="height:32px;padding:0 12px;background:#16a34a;color:#fff;border:none;border-radius:7px;font-size:11px;font-weight:600;cursor:pointer;min-width:60px">
                    Simpan
                </button>
            </div>
            <div style="font-size:9px;color:#64748b;margin-top:6px;line-height:1.4">
                💡 Setting ini akan ${isSpecific ? '<strong>hanya berlaku untuk pasangan mitra ini</strong>' : 'berlaku untuk <strong>semua mitra dengan level yang sama</strong>'}
            </div>
        </div>`;

        el.style.display = 'block';
        el.style.left = sx + 'px';
        el.style.top  = sy + 'px';
        // Simpan edge dengan from_level/to_level yang sudah di-resolve
        this._currentEdge    = { ...edge, from_level: fromLevel, to_level: toLevel };
        this._currentFeeType = isFlat ? 'flat' : 'percentage';
        window._affTree = this;
    }

    _switchFeeType(type) {
        this._currentFeeType = type;
        const isFlat = type === 'flat';
        document.getElementById('aff-fee-btn-pct').style.background  = !isFlat ? '#4f46e5' : '#f8fafc';
        document.getElementById('aff-fee-btn-pct').style.color        = !isFlat ? '#fff' : '#64748b';
        document.getElementById('aff-fee-btn-flat').style.background  = isFlat ? '#16a34a' : '#f8fafc';
        document.getElementById('aff-fee-btn-flat').style.color       = isFlat ? '#fff' : '#64748b';
        document.getElementById('aff-fee-label').textContent          = isFlat ? 'Nominal Fee (Rp)' : 'Persentase Fee (%)';
        const inp = document.getElementById('aff-fee-pct');
        inp.step = isFlat ? '1000' : '0.5';
        if (!isFlat) inp.max = '100';
        else inp.removeAttribute('max');
    }

    _hidePopup() {
        if (this._popup) this._popup.style.display = 'none';
        if (this.activeNode && this.nodeEls[this.activeNode.id]) {
            const p = this.nodeEls[this.activeNode.id];
            p.circle.setAttribute('r', p.R); p.sh.setAttribute('r', p.R);
            p.circle.setAttribute('filter', '');
        }
        if (this.activeEdge) {
            const er = this.edgeEls.find(e => e.edge === this.activeEdge);
            if (er) {
                er.path.setAttribute('stroke', this.activeEdge.has_fee ? '#16a34a' : '#94a3b8');
                er.path.setAttribute('stroke-width', this.activeEdge.has_fee ? '2' : '1.5');
            }
        }
        this.activeNode = null;
        this.activeEdge = null;
    }

    // ─── Actions ──────────────────────────────────────────────────────────────
    _goView(id) {
        this._hidePopup();
        window.location.href = `${this.opts.viewBase}/${id}`;
    }
    _openEditModal(id) {
        this._hidePopup();
        const node = this.nodes.find(n => n.id === id);
        if (node) document.dispatchEvent(new CustomEvent('affTree:editNode', { detail: { node } }));
    }
    _openAddModal(id) {
        this._hidePopup();
        const node = this.nodes.find(n => n.id === id);
        if (node) document.dispatchEvent(new CustomEvent('affTree:addBelow', { detail: { node } }));
    }
    _confirmDelete(id) {
        this._hidePopup();
        const node = this.nodes.find(n => n.id === id);
        if (node) document.dispatchEvent(new CustomEvent('affTree:deleteNode', { detail: { node } }));
    }
    async _saveFee() {
        const val  = parseFloat(document.getElementById('aff-fee-pct').value) || 0;
        const edge = this._currentEdge;
        const type = this._currentFeeType || 'percentage';
        if (!edge) return;

        if (!edge.from_level || !edge.to_level) {
            alert('Tidak dapat menyimpan: level mitra tidak teridentifikasi. Pastikan mitra memiliki program yang terdaftar.');
            return;
        }

        // Loading state
        const btn = document.getElementById('aff-fee-save-btn');
        if (btn) { btn.disabled = true; btn.innerHTML = '⏳'; }

        const fd = new FormData();
        fd.append('from_level', edge.from_level);
        fd.append('to_level',   edge.to_level);
        fd.append('fee_type',   type);
        fd.append('fee_value',  val);
        
        // PENTING: Kirim ID mitra untuk setting spesifik
        if (edge.from && edge.to) {
            fd.append('from_affiliator_id', edge.from);
            fd.append('to_affiliator_id',   edge.to);
        }

        try {
            const res = await fetch(this.opts.feeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN':     this.opts.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept':           'application/json',
                },
                body: fd,
            });

            let data;
            try {
                data = await res.json();
            } catch {
                const text = await res.text();
                console.error('Non-JSON response (status ' + res.status + '):', text.substring(0, 300));
                if (btn) { btn.disabled = false; btn.innerHTML = 'Simpan'; }
                alert('Server error ' + res.status + '. Coba refresh halaman.');
                return;
            }

            if (data.success) {
                this._hidePopup();
                // Update edge data di treeData agar popup berikutnya pakai nilai terbaru
                if (window._affTreeData) {
                    const e = window._affTreeData.edges.find(
                        x => x.from === edge.from && x.to === edge.to
                    );
                    if (e) {
                        e.fee_type  = type;
                        e.fee_value = val;
                        e.percentage = type === 'percentage' ? val : 0;
                        e.is_specific = true; // Mark sebagai setting spesifik
                    }
                }
                // Tampilkan notifikasi sukses singkat
                const notif = document.createElement('div');
                notif.style.cssText = 'position:fixed;top:20px;right:20px;z-index:99999;background:#16a34a;color:#fff;padding:10px 18px;border-radius:10px;font-size:13px;font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,0.15)';
                notif.textContent = '✓ ' + (data.message || 'Fee berhasil disimpan');
                document.body.appendChild(notif);
                setTimeout(() => notif.remove(), 2500);
                document.dispatchEvent(new CustomEvent('affTree:reload'));
            } else {
                if (btn) { btn.disabled = false; btn.innerHTML = 'Simpan'; }
                alert('Gagal: ' + (data.message || 'Unknown error'));
            }
        } catch (err) {
            console.error('Save fee error:', err);
            if (btn) { btn.disabled = false; btn.innerHTML = 'Simpan'; }
            alert('Gagal menyimpan fee. Silakan coba lagi.');
        }
    }

    // ─── Resize ───────────────────────────────────────────────────────────────
    _setupResize() {
        let t;
        const ro = new ResizeObserver(() => {
            this._hidePopup();
            clearTimeout(t);
            t = setTimeout(() => {
                this.positions = {};
                this._layout();
                this._draw();
            }, 200);
        });
        ro.observe(this.svg.parentElement || this.svg);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────
    _fmt(n) { return new Intl.NumberFormat('id-ID').format(Math.round(n)); }

    _color(slug) {
        const m = {
            'hm-master':  { fill: '#7c3aed', light: '#ede9fe' },
            'hm-leader':  { fill: '#4f46e5', light: '#e0e7ff' },
            'hm-partner': { fill: '#16a34a', light: '#dcfce7' },
            'hm-seller':  { fill: '#2563eb', light: '#dbeafe' },
            'hm-member':  { fill: '#9ca3af', light: '#f3f4f6' },
        };
        return m[slug] || m['hm-member'];
    }
}
