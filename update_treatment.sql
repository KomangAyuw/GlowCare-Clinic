USE glowcareclinic;
ALTER TABLE treatment ADD COLUMN durasi VARCHAR(50) DEFAULT '60 Menit' AFTER kategori;
DELETE FROM treatment;
INSERT INTO treatment (nama, kategori, durasi, deskripsi_panjang, gambar_url, status, urutan) VALUES 
('Chemical Peeling for Acne', 'Acne & Clear Skin', '45 Menit', 'A medical-grade exfoliating treatment that clears clogged pores, reduces inflammation, and prevents future breakouts by accelerating skin cell turnover.', 'asset/img/acne_peel.png', 'Aktif', 1),
('Laser Rejuvenation', 'Anti-Aging', '60 Menit', 'Utilizes advanced light technology to stimulate deep collagen production, smoothing fine lines and improving overall skin texture for a youthful lift.', 'asset/img/Laser.jpg', 'Aktif', 2),
('Radiance Glow Infusion', 'Brightening', '30 Menit', 'An instant brightening protocol delivering high-potency Vitamin C and antioxidants directly into the skin for a luminous, "glass skin" finish.', 'asset/img/glow_infusion.png', 'Aktif', 3),
('Botox & Neuromodulators', 'Anti-Aging', '15 Menit', 'Precision injections that temporarily relax dynamic facial muscles, softening wrinkles and preventing deeper lines from forming.', 'asset/img/treatmen3.png', 'Aktif', 4),
('Pico Laser Toning', 'Brightening', '45 Menit', 'Advanced picosecond technology targeted at breaking down hyperpigmentation and melasma for an even, bright complexion with minimal downtime.', 'asset/img/treatment5.png', 'Aktif', 5),
('Clinical Hair Restoration', 'Hair & Body Care', '90 Menit', 'Evidence-based protocols to stimulate hair follicles and scalp health using bio-stimulatory infusions and light therapy.', 'asset/img/haircareBody.png', 'Aktif', 6),
('Laser Hair Removal', 'Hair & Body Care', '30 Menit', 'Permanent reduction of unwanted hair using medical-grade cooling lasers, safe for various skin tones and body areas.', 'asset/img/treatment7.png', 'Aktif', 7),
('Medical Extraction Facial', 'Acne & Clear Skin', '60 Menit', 'A deep-cleansing medical protocol focused on professional manual extractions and sterile barrier repair for congested skin.', 'asset/img/acneTreatment.png', 'Aktif', 8),
('Dermal Filler Contouring', 'Anti-Aging', '45 Menit', 'Strategic volume restoration using hyaluronic acid to lift cheeks, define jawlines, and fill hollow areas for a balanced profile.', 'asset/img/treatment8.png', 'Aktif', 9);
