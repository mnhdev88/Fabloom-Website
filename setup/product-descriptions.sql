-- Fabloom — long-form product descriptions
-- Fills products.description, which was empty on all 29 rows.
-- Safe to re-run: each statement is keyed by slug and overwrites.

-- Bhagalpur Heritage Silk
UPDATE products SET description = 'Bhagalpur Heritage Silk is the cloth our region is named for. It is woven on traditional handlooms from silk yarn that keeps its natural irregularity, so the surface carries a fine slub rather than the flat, machine-even face of a power-loom silk. That slub is what gives the fabric its depth: light breaks across it instead of bouncing straight back, and the colour reads slightly differently as the cloth moves.

At 14 momme it sits in the middle of the silk weight range — substantial enough to hold a shape, fluid enough to fall in a soft column rather than standing away from the body. It tailors well into kurtas, saree blouses, jackets, drapes and lehenga panels, and it takes both traditional and contemporary dye palettes cleanly.

Woven to order at 44 inches, in a minimum length of 50 metres. Colour can be matched to a supplied shade reference; tell us the shade and the quantity when you enquire and we will confirm the lead time.' WHERE slug = 'bhagalpur-heritage-silk';

-- Raw Tussar Silk
UPDATE products SET description = 'Raw Tussar is a wild silk, reeled from cocoons rather than farmed the way mulberry silk is, and it comes off the loom with a warm golden cast that no dye has to create. The yarn is thicker and less regular than mulberry, which is exactly the point — the finished cloth has a dry, textured hand and a quiet gleam instead of a high shine.

At 16 momme it is the heavier of our two silks. It holds structure well, which makes it a natural choice for jackets, structured kurtas, dupattas and saree bodies, and it works particularly well in fusion pieces where a little body is wanted through the shoulder and sleeve.

Woven to order at 44 inches, minimum 50 metres. It can be supplied in its natural undyed gold or dyed to a shade you specify. Because tussar is a wild fibre, slight variation in slub and tone between lengths is characteristic of the cloth rather than a fault.' WHERE slug = 'raw-tussar-silk';

-- Hand-Woven Khadi Linen
UPDATE products SET description = 'Hand-woven khadi linen is spun and woven by hand, and the cloth shows it. The yarn thickness varies slightly along its length, so the weave carries an irregular slub across the whole surface — dense in places, open in others. Left undyed, it stays in the natural flax tones of oatmeal through to pale grey.

Linen is the fibre that improves with use: it starts crisp, softens with every wash, and stays cool against the skin because the fibre wicks moisture and lets air through. At 150 GSM this is a mid-weight cloth, right for shirting, kurtas, summer trousers, dresses and light curtaining, and hard-wearing enough to take years of laundering.

Supplied at 58 inches wide, minimum 50 metres, in natural undyed or dyed to a shade you specify. The slub and the small tonal shifts across a length are the character of hand-woven cloth, not a defect.' WHERE slug = 'hand-woven-khadi-linen';

-- Texture Furnishing Linen
UPDATE products SET description = 'Texture Furnishing Linen was developed for interiors rather than apparel. It is woven in a deliberately open construction with a heavier yarn, so the weave itself is visible from across a room — a coarse, honest texture that reads as a material rather than a surface.

At 220 GSM it is the heaviest cloth in our linen range. It has the weight to hang in deep, stable folds for curtains and blinds, and enough body to take upholstery, cushion covers, loose covers and roman blinds. The open weave keeps it from feeling stiff despite the weight, so it still drapes rather than boarding out.

Supplied at 58 inches wide, minimum 50 metres, in natural or dyed colourways. For upholstery use, tell us the application when you enquire — we can advise on backing and on how the open weave behaves over a frame.' WHERE slug = 'texture-furnishing-linen';

-- Solid Natural Linen — Colour Range
UPDATE products SET description = 'This is our solid linen programme: one consistent base cloth, offered in natural and ecru alongside a full dyed colour range. It exists for buyers who need to repeat an order months later and get the same cloth back — the construction, the handle and the shade depth are held to the same standard batch to batch.

At 150 GSM it is a versatile mid-weight, equally at home in shirting, dresses, kurtas, trousers and light furnishing. Dyeing is done through eco-safe processes, and shade depth is checked against the reference rather than judged by eye, so a second run matches the first.

Supplied at 58 inches wide, minimum 50 metres per colour. Send a shade reference — a Pantone number, a swatch, or a length of your existing cloth — and we will match it and confirm the lead time before you commit to the order.' WHERE slug = 'solid-natural-linen-colour-range';

-- Kota Doria Block Print
UPDATE products SET description = 'Kota Doria is a weave before it is a print. The cloth is built in a square check formed by alternating fine and thick yarns, which leaves tiny open windows across the whole surface — the reason a Kota length weighs almost nothing and reads as translucent when held up.

Onto that ground we hand block print with chemical-free dyes. Each colour is a separate carved wooden block, registered by eye and struck by hand, so the impression carries the slight variation in pressure and placement that tells you a person made it rather than a machine.

At 90 GSM and 44 inches wide this is a summer cloth — dupattas, saris, light kurtas, scarves and sheer overlays. Minimum 50 metres. Small differences in register and in the density of the impression between one repeat and the next are inherent to hand block printing.' WHERE slug = 'kota-doria-block-print';

-- Mul Chanderi Multiblock Print
UPDATE products SET description = 'Mul chanderi is a fine, sheer ground with a faint natural gloss, light enough to see through and still strong enough to print on. On this quality we use multiblock printing: rather than one block carrying the whole design, each colour is cut into its own block and struck in sequence, one after another, over the same length of cloth.

That method is slower and less forgiving — every block has to land against the last — but it is what allows several colours to sit together with clean edges instead of muddying into one another. The finished repeat has depth that a single-block print cannot reach.

At 80 GSM and 44 inches wide this is the lightest cloth we make, suited to dupattas, saris, layering pieces and sheer curtaining. Minimum 50 metres, printed to order. Slight variation in register between repeats is characteristic of hand block work.' WHERE slug = 'mul-chanderi-multiblock-print';

-- Linen Print #1 — Coral Blossom on Slate
UPDATE products SET description = 'Coral blossom clusters in coral and peach, massed across a slate-blue ground. It is a full-cover repeat with real movement in it — the blossoms sit at different angles and depths rather than marching in a grid, so the eye keeps travelling across the cloth.

The cool slate base does the work here: it stops the warm florals from turning sweet and keeps the whole thing wearable. Cuts well for dresses, wide-leg trousers and blouses, and holds up at furnishing scale for cushions and light curtaining.

Printed on our own pure linen at 140 GSM, 44 inches wide. Minimum order 50 metres. The slate ground can be shifted warmer or cooler if it needs to sit with an existing range — ask for a proof strike before the run.' WHERE slug = 'linen-print-1';

-- Linen Print #2 — Dressed Bear Childrenswear
UPDATE products SET description = 'Storybook bears in coats and hats, scattered at a comfortable distance across natural linen. The characters are drawn with a light line and a limited palette, so the print stays gentle rather than loud — closer to an illustrated page than a cartoon.

Developed specifically for childrenswear and nursery furnishing. The scale suits small garments without the design being cut apart, and it works equally well for cot bumpers, curtains, quilt covers and soft toys.

The ground is our pure linen at 140 GSM, supplied 44 inches wide. Minimum order 50 metres. Because childrenswear cloth gets washed hard, ask us for a wash-tested sample before you commit; we would rather you saw how it comes back than take our word for it.' WHERE slug = 'linen-print-2';

-- Linen Print #3 — Berry Vine on Slate
UPDATE products SET description = 'A trailing leaf-and-berry vine drawn in ecru over a slate-blue ground. The vine runs in one direction, which makes this a directional print — worth knowing at the cutting table, and worth using deliberately when you want length in a garment.

Quiet, restrained and very easy to place. It is the print in this range that behaves most like a plain: it will sit next to a solid without fighting it, and it reads almost as texture from a distance.

Digitally printed on pure linen, 140 GSM, 44 inches wide. Minimum order 50 metres. Note the direction of the vine when you plan a lay — a one-way print costs more cloth than a tossed one, and it is worth allowing for that in your costing.' WHERE slug = 'linen-print-3';

-- Linen Print #4 — Scooter Novelty Print
UPDATE products SET description = 'Orange scooters, tilted at different angles, on a pale cream ground. A conversational print in the proper sense — the kind of design someone notices and asks about, built around a single motif rather than a botanical repeat.

Best at shirting scale, where the motif can be read on a chest or a cuff, and popular for kidswear where a recognisable object beats an abstract pattern. Also works for pocket linings, facings and other places a small surprise is welcome.

Printed to order on pure linen at 140 GSM, 44 inches wide. Minimum 50 metres. The scooter colour is the easiest thing in this design to change; if orange is wrong for your range, tell us the shade and we will proof it.' WHERE slug = 'linen-print-4';

-- Linen Print #5 — Scattered Posy on Slub
UPDATE products SET description = 'Small mixed posies scattered across a natural slub linen. The flowers are loosely drawn and the palette is deliberately muted, so the print sits down into the cloth instead of sitting on top of it — the slub of the linen shows through and becomes part of the design.

One of the most versatile pieces in the range. It suits dresses, blouses, children''s clothing, aprons and light furnishing, and it carries a busy garment shape without the print and the pattern-cutting competing.

Our pure linen ground, 140 GSM, 44 inches wide, printed to order in a minimum of 50 metres. The slub in the base cloth varies slightly from batch to batch, and because this print is deliberately soft, that variation reads as depth rather than inconsistency.' WHERE slug = 'linen-print-5';

-- Linen Print #6 — Painterly Bloom on Periwinkle
UPDATE products SET description = 'Large painterly blooms in rose, saffron and jade over a periwinkle ground, brushed rather than outlined, with the colour bleeding softly at the edges the way it does in a wet painting. This is the boldest design we print.

The scale asks for simple shapes: a shift dress, a wide trouser, a long kaftan, a single unlined curtain. Give it a plain silhouette and it carries the whole garment on its own. Cut it into a complicated pattern and the artwork gets lost.

Printed on pure linen at 140 GSM, 44 inches wide, minimum 50 metres. At this scale the repeat length matters to your cutting plan — ask us for the exact repeat measurement before you order so you can calculate yardage properly.' WHERE slug = 'linen-print-6';

-- Linen Print #7 — Botanical Trail on Cream
UPDATE products SET description = 'A dense botanical trail in magenta and green on cream, with fine line detail through the stems and leaf veins that only digital printing holds cleanly at this density. Coverage is close to all-over, with very little ground showing.

Rich, saturated and confident. It works for dresses, kurtas, jackets and cushion covers, and because the repeat is tight there is no awkward placement to plan around — you can cut it in any direction and it still reads correctly.

Pure linen ground at 140 GSM, 44 inches wide. Minimum order 50 metres. Digital printing is what makes this density possible: the fine venation in the leaves would fill in and blur if the design were struck from a block or pulled through a screen.' WHERE slug = 'linen-print-7';

-- Linen Print #8 — Character Print on Mint
UPDATE products SET description = 'A cartoon character repeat on a soft mint linen ground, drawn at a small scale and evenly spaced for childrenswear and nursery use.

Please note: this design is based on licensed artwork. Confirm current availability and any territory or usage restrictions with us before you plan an order around it — we will tell you honestly what can and cannot be supplied.

The base cloth is our pure linen at 140 GSM, 44 inches wide, printed to order with a 50 metre minimum. Lead time depends on clearance as well as production, so allow more time on this design than on the rest of the range.' WHERE slug = 'linen-print-8';

-- Linen Print #9 — Ochre Sprig on Texture
UPDATE products SET description = 'Ochre sprigs over a textured cream ground, with a fine script overlay running behind the botanical layer. From across a room it reads as a warm neutral texture; up close the second layer of detail appears.

That two-distance quality makes it useful where a print has to work both as a garment and as a surface — shirting, dresses, table linen, cushions and blinds. The ochre is warm enough to sit against wood and natural tones without clashing.

Printed digitally on pure linen, 140 GSM, 44 inches wide, minimum 50 metres. The script layer sits at a lower opacity than the sprigs; if you want it stronger or removed altogether, both are straightforward changes at proofing stage.' WHERE slug = 'linen-print-9';

-- Linen Print #10 — Mixed Posy on White
UPDATE products SET description = 'Red, blue and yellow posies on a bright white linen. The palette is primary and unapologetic, and the white ground keeps it crisp instead of heavy — this is the freshest print in the range.

Made for summer shirting, sundresses, children''s clothes and table linen. White grounds do show wear faster than coloured ones, so it suits pieces that will be laundered often and are meant to look laundered.

Our pure linen at 140 GSM, 44 inches wide, printed to order. Minimum 50 metres. White grounds print the cleanest of anything we do — there is no base tone under the pigment, so the reds and blues come out at full strength.' WHERE slug = 'linen-print-10';

-- Linen Print #11 — Rose Bouquet on Blush
UPDATE products SET description = 'Rose and foliage bouquets on a blush ground, rendered soft-focus so the edges of each bloom melt slightly into the background. There is no hard outline anywhere in the design, which is what gives it its painted, slightly faded quality.

Romantic without being saccharine, and easy to wear because the tonal range is narrow. Suits dresses, blouses, nightwear and bedroom furnishing — anywhere a print should feel soft rather than graphic.

Printed on pure linen at 140 GSM, 44 inches wide, minimum order 50 metres. Soft-focus artwork is unforgiving of colour drift, which is the argument for printing it digitally: the hundredth metre matches the first without a shade break in between.' WHERE slug = 'linen-print-11';

-- Linen Print #12 — Goose Novelty on Brown
UPDATE products SET description = 'White geese marching across a chocolate-brown ground. The motif is simple and the spacing is regular, so the humour comes from the repetition rather than from the drawing.

A characterful conversational print that has done well in furnishing — kitchen curtains, aprons, tea towels and cushions — and equally well in shirting for anyone who wants a print with a sense of humour in it. The dark ground is forgiving and hides marks.

Pure linen, 140 GSM, 44 inches wide, printed to order in a minimum of 50 metres. The brown ground is available in a lighter tan if the chocolate is too heavy for your application — say so when you enquire and we will send both.' WHERE slug = 'linen-print-12';

-- Linen Print #13 — Blue Buti Repeat
UPDATE products SET description = 'A neat blue flower-head buti repeat on natural linen. The buti — a small isolated motif set in a regular grid — is one of the oldest layouts in Indian textiles, and here it is printed digitally so every motif lands in exact register down the length.

Traditional in layout, clean in execution. It suits kurtas, saris, dupattas and shirting, and it is the design in this range that works best when you want something recognisably Indian without it being ornate.

Printed on our pure linen base at 140 GSM, 44 inches wide, minimum 50 metres. Buti spacing can be opened up or tightened to change how busy the cloth reads without redrawing the motif; it is one of the cheapest adjustments to make.' WHERE slug = 'linen-print-13';

-- Linen Print #14 — Daisy Sprig on Taupe
UPDATE products SET description = 'Small white daisy sprigs on a warm taupe ground. Understated to the point of being almost plain, with just enough motif to lift it above a solid.

This is the print to reach for when something else in the garment is doing the talking — a strong silhouette, a heavy trim, a bold lining. It coordinates with almost anything, and the taupe ground is warm enough to sit comfortably next to natural linen and undyed cloth.

Our pure linen ground at 140 GSM, 44 inches wide. Minimum order 50 metres. If you are using this as a coordinate, we can match the taupe against a solid from our Solid Natural Linen range so the two sit together properly.' WHERE slug = 'linen-print-14';

-- Linen Print #15 — Tulip Posy on Oatmeal
UPDATE products SET description = 'Tulip and forget-me-not posies on an oatmeal ground, in a gentle vintage palette of dusty pink, soft blue and sage. The drawing style is deliberately old-fashioned — the kind of floral you would find on a folded square of cloth in a drawer.

Works for dresses, blouses, children''s clothing and quilting, and it pairs naturally with plain oatmeal or ecru linen from our solid range for facings, bindings and contrast panels.

Printed to order on pure linen, 140 GSM, 44 inches wide, minimum 50 metres. The vintage palette is intentionally low-contrast; pushing the colours brighter is possible but changes the character of the design, so we would proof it before running it.' WHERE slug = 'linen-print-15';

-- Linen Print #16 — Hen Novelty on Cream
UPDATE products SET description = 'Hens and roosters walking across a cream ground, drawn with visible line work and filled in warm rust, cream and charcoal. A country-kitchen novelty print, and consistently one of the most requested designs we print.

Most of its use is in furnishing — aprons, tea towels, kitchen curtains, cushions and table linen — but it cuts well for shirting too. The cream ground keeps it bright without the starkness of a true white.

Pure linen at 140 GSM, 44 inches wide, printed to order. Minimum 50 metres. This one moves quickly, so check current stock of the printed base with us rather than assuming immediate availability.' WHERE slug = 'linen-print-16';

-- Linen Print #17 — Floral on Chrome Yellow
UPDATE products SET description = 'A floral repeat on a chrome yellow ground: hot, saturated and impossible to ignore. Yellow at this intensity is rare in printed linen because it is difficult to hold evenly across a run, which is one reason this design is printed digitally rather than from screens.

Use it where you want the colour to be the point — a summer dress, a jacket lining, a single statement cushion, a set of blinds in a dark room. It will lift everything around it.

Printed on pure linen at 140 GSM, 44 inches wide, minimum order 50 metres. Strong yellows are the hardest colour to hold evenly across a long run; we proof this design against a physical reference rather than a screen before printing.' WHERE slug = 'linen-print-17';

-- Linen Print #18 — Rose Bouquet on White
UPDATE products SET description = 'Rose bouquets on a clean white ground. The drawing is fuller and more defined than our blush-ground rose print, with visible petal edges and deeper greens in the foliage, so it reads sharper and more formal.

A classic that keeps selling. It suits occasion dresses, blouses, table linen and bedroom furnishing, and it has the kind of longevity that makes it worth holding as a repeat line rather than a season piece.

Our pure linen ground, 140 GSM, 44 inches wide, printed to order in a minimum of 50 metres. We hold this artwork as a repeat line, so a reorder in six months will match the cloth you have on the shelf.' WHERE slug = 'linen-print-18';

-- Linen Print #19 — Watercolour Petal on Cream
UPDATE products SET description = 'Watercolour petals on cream, laid down in soft washes with no outline at all — the shapes are defined by where the colour is denser, exactly as they would be in a real watercolour. The palette is pale and the coverage is light.

The most delicate print in the range. It suits summer dresses, scarves, blouses and light curtaining, and it behaves well in gathers and pleats because there is no hard motif to break up.

Printed on pure linen at 140 GSM, 44 inches wide. Minimum 50 metres. Pale washes show the linen slub through them more than a dense print does — that is intended here, but ask for a cutting if you need to see it before ordering.' WHERE slug = 'linen-print-19';

-- Hand Brush Floral Work
UPDATE products SET description = 'Hand brush work is exactly what the name says. There is no block, no screen and no printer — an artisan works directly on the stretched cloth with a brush, painting each stem, leaf and petal by hand, one length at a time.

The consequence is that no two lengths are identical, and no two repeats within a length are either. A petal is fuller here, a stem leans differently there. If you need a design that reproduces exactly, this is not that cloth; if you want the value of something visibly made by hand, this is where it comes from.

Painted on pure linen at 160 GSM, 44 inches wide — a little heavier than our printed linens, because the base has to hold the brush without the colour spreading. Minimum 50 metres. Because each length is painted individually, lead times are longer than for printed cloth; we will confirm the schedule when you enquire.' WHERE slug = 'hand-brush-floral-work';

-- Hand-Crafted Silk Saree — Zari Border
UPDATE products SET description = 'A handwoven pure silk saree finished with a traditional gold zari border. The body is woven on the handloom in Bhagalpur and the zari is worked into the border on the loom rather than applied afterwards, so the border is part of the cloth rather than a trim sitting on top of it.

Each saree is made to order. The body colour, the border width and the zari pattern are all specified by you, which means the piece you receive has not been made before and will not be made again unless you ask for it.

Sold as a single finished piece, not by the metre, so the 50 metre fabric minimum does not apply. Lead time depends on the border complexity and the loom schedule — tell us the combination you want and we will confirm a date before you commit.' WHERE slug = 'hand-crafted-silk-saree-zari';

-- Hand-Crafted Saree — Embroidered Border
UPDATE products SET description = 'A handwoven saree with a hand-embroidered multicolour border. The body is woven first, then the border is embroidered by hand, stitch by stitch, in a colour combination chosen for the piece.

It is lighter than our zari saree and easier to wear for long stretches — breathable, soft in the fold, and without the weight that metallic thread adds. That makes it a day saree rather than an occasion-only one, though the border is worked finely enough to hold its own at an event.

Made in limited runs and to order. Sold as a finished piece rather than by the metre, so the 50 metre fabric minimum does not apply. Because the border is embroidered by hand, small differences between pieces are expected and are part of what you are buying.' WHERE slug = 'hand-crafted-saree-embroidered';
