-- Migration: AllVideoShare -> CineFrame
-- Run once on the aidff database (shortfilm_ prefix)
-- -------------------------------------------------------

-- 1. Migrate categories (keep original IDs)
INSERT INTO shortfilm_cineframe_categories (id, name, ordering, published)
SELECT
    id,
    TRIM(name),
    ordering,
    state
FROM shortfilm_allvideoshare_categories
ON DUPLICATE KEY UPDATE name = VALUES(name), ordering = VALUES(ordering), published = VALUES(published);

-- 2. Migrate videos
-- type: if youtube URL exists → youtube, vimeo → vimeo, else embed (dailymotion/mp4/etc)
-- source: first non-empty of youtube, vimeo, video
INSERT INTO shortfilm_cineframe_videos
    (title, catid, type, source, thumb, description, width, published, ordering, created, created_by, modified)
SELECT
    TRIM(v.title),
    v.catid,
    CASE
        WHEN v.youtube <> '' THEN 'youtube'
        WHEN v.vimeo   <> '' THEN 'vimeo'
        ELSE 'embed'
    END AS type,
    CASE
        WHEN v.youtube <> '' THEN v.youtube
        WHEN v.vimeo   <> '' THEN v.vimeo
        ELSE v.video
    END AS source,
    COALESCE(NULLIF(v.thumb, ''), '') AS thumb,
    COALESCE(NULLIF(v.description, ''), NULL) AS description,
    0 AS width,
    v.state AS published,
    v.ordering,
    v.created_date,
    v.created_by,
    v.updated_date
FROM shortfilm_allvideoshare_videos v
ORDER BY v.id;
