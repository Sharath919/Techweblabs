-- TechWebLabs — Supabase schema for blog automation

-- Profiles (Supabase auth)
CREATE TABLE IF NOT EXISTS public.profiles (
  id uuid PRIMARY KEY REFERENCES auth.users(id) ON DELETE CASCADE,
  name text,
  email text NOT NULL DEFAULT '',
  role text NOT NULL DEFAULT 'user' CHECK (role IN ('user', 'editor', 'admin')),
  created_at timestamptz DEFAULT now()
);

ALTER TABLE public.profiles ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Users can read own profile"
  ON public.profiles FOR SELECT TO authenticated
  USING (auth.uid() = id);

CREATE POLICY "Users can update own profile"
  ON public.profiles FOR UPDATE TO authenticated
  USING (auth.uid() = id) WITH CHECK (auth.uid() = id);

CREATE POLICY "Users can insert own profile"
  ON public.profiles FOR INSERT TO authenticated
  WITH CHECK (auth.uid() = id);

CREATE OR REPLACE FUNCTION public.handle_new_user()
RETURNS trigger AS $$
BEGIN
  INSERT INTO public.profiles (id, name, email, role)
  VALUES (
    NEW.id,
    COALESCE(NEW.raw_user_meta_data->>'name', NEW.raw_user_meta_data->>'full_name'),
    COALESCE(NEW.email, ''),
    'user'
  )
  ON CONFLICT (id) DO NOTHING;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;
CREATE TRIGGER on_auth_user_created
  AFTER INSERT ON auth.users
  FOR EACH ROW EXECUTE FUNCTION public.handle_new_user();

-- Admin users
CREATE TABLE IF NOT EXISTS public.admin_users (
  id uuid PRIMARY KEY REFERENCES public.profiles(id) ON DELETE CASCADE,
  role text NOT NULL DEFAULT 'admin',
  created_at timestamptz DEFAULT now()
);

ALTER TABLE public.admin_users ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Users can check own admin status"
  ON public.admin_users FOR SELECT TO authenticated
  USING (auth.uid() = id);

CREATE OR REPLACE FUNCTION public.is_site_admin()
RETURNS boolean
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path = public
AS $$
  SELECT EXISTS (
    SELECT 1 FROM public.admin_users au WHERE au.id = auth.uid()
  )
  OR lower(trim(coalesce(
    NULLIF(trim(auth.jwt() ->> 'email'), ''),
    NULLIF(trim(auth.jwt() -> 'user_metadata' ->> 'email'), ''),
    ''
  ))) IN ('admin@techweblabs.com', 'sharathchand19141@gmail.com', 'sharathbroyt@gmail.com');
$$;

REVOKE ALL ON FUNCTION public.is_site_admin() FROM PUBLIC;
GRANT EXECUTE ON FUNCTION public.is_site_admin() TO authenticated;

CREATE OR REPLACE FUNCTION public.is_article_writer()
RETURNS boolean
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path = public
AS $$
  SELECT public.is_site_admin()
  OR EXISTS (
    SELECT 1 FROM public.profiles p
    WHERE p.id = auth.uid() AND p.role IN ('editor', 'admin')
  );
$$;

GRANT EXECUTE ON FUNCTION public.is_article_writer() TO authenticated;

CREATE OR REPLACE FUNCTION public.update_updated_at()
RETURNS trigger LANGUAGE plpgsql AS $$
BEGIN
  NEW.updated_at = now();
  RETURN NEW;
END;
$$;

-- Blog posts (mirrors legacy MySQL blog_posts)
CREATE TABLE IF NOT EXISTS public.blog_posts (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  legacy_id integer,
  slug text UNIQUE NOT NULL,
  title text NOT NULL,
  meta_title text,
  meta_description text,
  meta_keywords text,
  excerpt text,
  content text NOT NULL DEFAULT '',
  featured_image text,
  og_image text,
  author_id uuid REFERENCES auth.users(id) ON DELETE SET NULL,
  author_name text DEFAULT 'TechWebLabs',
  status text NOT NULL DEFAULT 'draft' CHECK (status IN ('draft', 'published', 'archived')),
  published_at timestamptz,
  views integer DEFAULT 0,
  seo_focus_keyword text,
  schema_type text DEFAULT 'BlogPosting',
  reading_time integer,
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

ALTER TABLE public.blog_posts ENABLE ROW LEVEL SECURITY;

DROP TRIGGER IF EXISTS blog_posts_updated_at ON public.blog_posts;
CREATE TRIGGER blog_posts_updated_at
  BEFORE UPDATE ON public.blog_posts
  FOR EACH ROW EXECUTE FUNCTION public.update_updated_at();

CREATE POLICY "Public read published blog_posts"
  ON public.blog_posts FOR SELECT TO anon, authenticated
  USING (status = 'published');

CREATE POLICY "Writers read own blog_posts"
  ON public.blog_posts FOR SELECT TO authenticated
  USING (auth.uid() = author_id);

CREATE POLICY "Site admins read all blog_posts"
  ON public.blog_posts FOR SELECT TO authenticated
  USING (public.is_site_admin());

CREATE POLICY "Writers insert blog_posts"
  ON public.blog_posts FOR INSERT TO authenticated
  WITH CHECK (public.is_article_writer());

CREATE POLICY "Writers update own drafts"
  ON public.blog_posts FOR UPDATE TO authenticated
  USING (public.is_site_admin() OR (auth.uid() = author_id AND public.is_article_writer()))
  WITH CHECK (public.is_site_admin() OR public.is_article_writer());

CREATE POLICY "Site admins full access blog_posts"
  ON public.blog_posts FOR ALL TO authenticated
  USING (public.is_site_admin()) WITH CHECK (public.is_site_admin());

GRANT SELECT ON public.blog_posts TO anon, authenticated;
GRANT INSERT, UPDATE, DELETE ON public.blog_posts TO authenticated;
GRANT ALL ON public.blog_posts TO service_role;

-- Blog categories
CREATE TABLE IF NOT EXISTS public.blog_categories (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  legacy_id integer,
  name text NOT NULL,
  slug text UNIQUE NOT NULL,
  description text,
  parent_id uuid REFERENCES public.blog_categories(id) ON DELETE SET NULL,
  created_at timestamptz DEFAULT now()
);

ALTER TABLE public.blog_categories ENABLE ROW LEVEL SECURITY;
CREATE POLICY "Public read blog_categories" ON public.blog_categories FOR SELECT TO anon, authenticated USING (true);
CREATE POLICY "Admins manage blog_categories" ON public.blog_categories FOR ALL TO authenticated
  USING (public.is_site_admin()) WITH CHECK (public.is_site_admin());
GRANT SELECT ON public.blog_categories TO anon, authenticated;
GRANT ALL ON public.blog_categories TO service_role;

CREATE TABLE IF NOT EXISTS public.blog_post_categories (
  post_id uuid REFERENCES public.blog_posts(id) ON DELETE CASCADE,
  category_id uuid REFERENCES public.blog_categories(id) ON DELETE CASCADE,
  PRIMARY KEY (post_id, category_id)
);

ALTER TABLE public.blog_post_categories ENABLE ROW LEVEL SECURITY;
CREATE POLICY "Public read blog_post_categories" ON public.blog_post_categories FOR SELECT TO anon, authenticated USING (true);
CREATE POLICY "Admins manage blog_post_categories" ON public.blog_post_categories FOR ALL TO authenticated
  USING (public.is_site_admin()) WITH CHECK (public.is_site_admin());
GRANT SELECT ON public.blog_post_categories TO anon, authenticated;
GRANT ALL ON public.blog_post_categories TO service_role;

-- Blog tags
CREATE TABLE IF NOT EXISTS public.blog_tags (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  legacy_id integer,
  name text NOT NULL,
  slug text UNIQUE NOT NULL,
  created_at timestamptz DEFAULT now()
);

ALTER TABLE public.blog_tags ENABLE ROW LEVEL SECURITY;
CREATE POLICY "Public read blog_tags" ON public.blog_tags FOR SELECT TO anon, authenticated USING (true);
CREATE POLICY "Admins manage blog_tags" ON public.blog_tags FOR ALL TO authenticated
  USING (public.is_site_admin()) WITH CHECK (public.is_site_admin());
GRANT SELECT ON public.blog_tags TO anon, authenticated;
GRANT ALL ON public.blog_tags TO service_role;

CREATE TABLE IF NOT EXISTS public.blog_post_tags (
  post_id uuid REFERENCES public.blog_posts(id) ON DELETE CASCADE,
  tag_id uuid REFERENCES public.blog_tags(id) ON DELETE CASCADE,
  PRIMARY KEY (post_id, tag_id)
);

ALTER TABLE public.blog_post_tags ENABLE ROW LEVEL SECURITY;
CREATE POLICY "Public read blog_post_tags" ON public.blog_post_tags FOR SELECT TO anon, authenticated USING (true);
CREATE POLICY "Admins manage blog_post_tags" ON public.blog_post_tags FOR ALL TO authenticated
  USING (public.is_site_admin()) WITH CHECK (public.is_site_admin());
GRANT SELECT ON public.blog_post_tags TO anon, authenticated;
GRANT ALL ON public.blog_post_tags TO service_role;

-- Publishing schedule (card_name = topic for TechWebLabs)
CREATE TABLE IF NOT EXISTS public.publishing_schedule (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  card_name text NOT NULL,
  template_type text NOT NULL DEFAULT 'seo-blog',
  scheduled_date date NOT NULL,
  status text NOT NULL DEFAULT 'pending' CHECK (status IN ('pending', 'processing', 'done', 'failed')),
  post_id uuid REFERENCES public.blog_posts(id) ON DELETE SET NULL,
  error_text text,
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

ALTER TABLE public.publishing_schedule ENABLE ROW LEVEL SECURITY;
CREATE POLICY "Admins manage publishing_schedule"
  ON public.publishing_schedule FOR ALL TO authenticated
  USING (public.is_site_admin()) WITH CHECK (public.is_site_admin());
GRANT SELECT, INSERT, UPDATE, DELETE ON public.publishing_schedule TO authenticated;
GRANT ALL ON public.publishing_schedule TO service_role;

DROP TRIGGER IF EXISTS publishing_schedule_updated_at ON public.publishing_schedule;
CREATE TRIGGER publishing_schedule_updated_at
  BEFORE UPDATE ON public.publishing_schedule
  FOR EACH ROW EXECUTE FUNCTION public.update_updated_at();

-- AI config
CREATE TABLE IF NOT EXISTS public.ai_config (
  key text PRIMARY KEY,
  value jsonb NOT NULL DEFAULT '""'::jsonb,
  updated_at timestamptz DEFAULT now()
);

ALTER TABLE public.ai_config ENABLE ROW LEVEL SECURITY;
CREATE POLICY "Authenticated read ai_config"
  ON public.ai_config FOR SELECT TO authenticated USING (true);
CREATE POLICY "Admins manage ai_config"
  ON public.ai_config FOR ALL TO authenticated
  USING (public.is_site_admin()) WITH CHECK (public.is_site_admin());
GRANT SELECT, INSERT, UPDATE, DELETE ON public.ai_config TO authenticated;
GRANT ALL ON public.ai_config TO service_role;

INSERT INTO public.ai_config (key, value) VALUES
  ('automation_enabled', 'true'),
  ('article_machine_prompt_default', '""'),
  ('article_machine_prompt_seo_blog', '""'),
  ('article_machine_prompt_comparison', '""'),
  ('article_machine_prompt_cost_guide', '""'),
  ('persona_system_prompt', '""'),
  ('persona_welcome_message', '"Hi! I''m here to help you find the right app development solution. What are you building?"'),
  ('last_cron_run', '""')
ON CONFLICT (key) DO NOTHING;

-- API usage log
CREATE TABLE IF NOT EXISTS public.api_usage_log (
  id uuid DEFAULT gen_random_uuid() PRIMARY KEY,
  post_id uuid REFERENCES public.blog_posts(id) ON DELETE SET NULL,
  schedule_id uuid REFERENCES public.publishing_schedule(id) ON DELETE SET NULL,
  provider text NOT NULL CHECK (provider IN ('openai', 'claude', 'replicate')),
  model text NOT NULL,
  operation text NOT NULL,
  input_tokens integer DEFAULT 0,
  output_tokens integer DEFAULT 0,
  total_tokens integer DEFAULT 0,
  cost_usd numeric(10, 6) DEFAULT 0,
  duration_ms integer DEFAULT 0,
  success boolean DEFAULT true,
  error_text text,
  prompt_key text,
  created_at timestamptz DEFAULT now()
);

ALTER TABLE public.api_usage_log ENABLE ROW LEVEL SECURITY;
CREATE POLICY "Admins read api_usage_log" ON public.api_usage_log FOR SELECT TO authenticated USING (public.is_site_admin());
GRANT ALL ON public.api_usage_log TO service_role;
GRANT SELECT ON public.api_usage_log TO authenticated;

-- Lead captures (persona widget)
CREATE TABLE IF NOT EXISTS public.lead_captures (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name text,
  email text,
  phone text,
  company text,
  requirement text,
  budget text,
  timeline text,
  source_page text DEFAULT '/',
  persona_transcript jsonb DEFAULT '[]'::jsonb,
  qualification_score integer,
  status text DEFAULT 'new' CHECK (status IN ('new', 'contacted', 'qualified', 'closed')),
  created_at timestamptz DEFAULT now()
);

ALTER TABLE public.lead_captures ENABLE ROW LEVEL SECURITY;
CREATE POLICY "Anyone insert lead_captures" ON public.lead_captures FOR INSERT TO anon, authenticated WITH CHECK (true);
CREATE POLICY "Admins read lead_captures" ON public.lead_captures FOR SELECT TO authenticated USING (public.is_site_admin());
CREATE POLICY "Admins update lead_captures" ON public.lead_captures FOR UPDATE TO authenticated USING (public.is_site_admin());
GRANT INSERT ON public.lead_captures TO anon, authenticated;
GRANT ALL ON public.lead_captures TO service_role;

-- Site pages config (new pillar/clone pages via Next.js)
CREATE TABLE IF NOT EXISTS public.site_pages (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  slug text UNIQUE NOT NULL,
  page_type text NOT NULL DEFAULT 'service' CHECK (page_type IN ('pillar', 'service', 'clone')),
  title text NOT NULL,
  meta_title text,
  meta_description text,
  meta_keywords text,
  target_keyword text,
  hero_title text,
  hero_subtitle text,
  content_json jsonb DEFAULT '{}'::jsonb,
  status text NOT NULL DEFAULT 'draft' CHECK (status IN ('draft', 'published')),
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

ALTER TABLE public.site_pages ENABLE ROW LEVEL SECURITY;
CREATE POLICY "Public read published site_pages" ON public.site_pages FOR SELECT TO anon, authenticated USING (status = 'published');
CREATE POLICY "Admins manage site_pages" ON public.site_pages FOR ALL TO authenticated
  USING (public.is_site_admin()) WITH CHECK (public.is_site_admin());
GRANT SELECT ON public.site_pages TO anon, authenticated;
GRANT ALL ON public.site_pages TO service_role;

-- Storage bucket for blog images
INSERT INTO storage.buckets (id, name, public, file_size_limit, allowed_mime_types)
VALUES ('blog-images', 'blog-images', true, 5242880, ARRAY['image/jpeg', 'image/png', 'image/webp'])
ON CONFLICT (id) DO NOTHING;

CREATE POLICY "Public read blog images" ON storage.objects FOR SELECT USING (bucket_id = 'blog-images');
CREATE POLICY "Writers upload blog images" ON storage.objects FOR INSERT TO authenticated
  WITH CHECK (bucket_id = 'blog-images' AND public.is_article_writer());

GRANT ALL ON public.profiles TO service_role;
GRANT ALL ON public.admin_users TO service_role;
