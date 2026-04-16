-- BerryCloud Database Schema
-- Run this in your MySQL/MariaDB database

CREATE DATABASE IF NOT EXISTS berrycloud CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE berrycloud;

-- =============================================
-- TABLE: recipes
-- =============================================
CREATE TABLE IF NOT EXISTS recipes (
  id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  title       VARCHAR(255)    NOT NULL,
  description TEXT            NOT NULL,
  ingredients TEXT            NOT NULL,
  steps       TEXT            NOT NULL,
  berry_type  VARCHAR(50)     NOT NULL DEFAULT 'Berry',
  prep_time   SMALLINT UNSIGNED        DEFAULT NULL,
  servings    TINYINT UNSIGNED         DEFAULT NULL,
  image_url   VARCHAR(500)             DEFAULT NULL,
  created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- SEED DATA: 2 sample recipes
-- =============================================
INSERT INTO recipes (title, description, ingredients, steps, berry_type, prep_time, servings, image_url)
VALUES
(
  'Strawberry Shortcake',
  'A classic, crowd-pleasing dessert with fluffy biscuits, fresh strawberries, and billowy whipped cream. Perfect for summer gatherings.',
  '2 cups fresh strawberries, hulled and sliced
3 tbsp sugar (for strawberries)
2 cups all-purpose flour
1 tbsp baking powder
1/2 tsp salt
1/4 cup sugar (for biscuits)
1/2 cup cold unsalted butter, cubed
3/4 cup heavy cream
1 cup heavy whipping cream (for topping)
2 tbsp powdered sugar',
  'Toss sliced strawberries with 3 tbsp sugar in a bowl. Set aside for 30 minutes to macerate.
Preheat oven to 425°F (220°C). Line a baking sheet with parchment paper.
Whisk together flour, baking powder, salt, and 1/4 cup sugar in a large bowl.
Cut in cold butter with a pastry cutter until mixture resembles coarse crumbs.
Stir in heavy cream until just combined. Do not overmix.
Drop spoonfuls of dough onto the prepared baking sheet to form 6 biscuits.
Bake for 12–15 minutes until golden brown. Let cool slightly.
Whip the heavy whipping cream with powdered sugar until soft peaks form.
Split each biscuit in half. Layer with strawberries and whipped cream. Serve immediately.',
  'Strawberry',
  30,
  6,
  'https://images.unsplash.com/photo-1464305795204-6f5bbfc7fb81?w=600&q=80'
),
(
  'Blueberry Lemon Tart',
  'A buttery tart shell filled with silky lemon curd and topped with a generous pile of fresh blueberries. Elegant, tangy, and irresistible.',
  '1 1/2 cups all-purpose flour
1/2 cup powdered sugar
1/4 tsp salt
1/2 cup cold unsalted butter, cubed
1 egg yolk
2 tbsp ice water
4 large eggs
3/4 cup granulated sugar
1/2 cup fresh lemon juice (about 3–4 lemons)
1 tbsp lemon zest
1/2 cup unsalted butter, cut into pieces
2 cups fresh blueberries
2 tbsp apricot jam (for glaze)',
  'Make the tart shell: mix flour, powdered sugar, and salt. Cut in cold butter until crumbly.
Add egg yolk and ice water; mix until dough just comes together. Flatten into a disc, wrap, and chill for 30 minutes.
Preheat oven to 375°F (190°C). Roll out dough and press into a 9-inch tart pan.
Blind bake the shell with parchment and pie weights for 15 minutes, then remove weights and bake 10 more minutes until golden.
Make the lemon curd: whisk eggs, sugar, lemon juice, and zest in a saucepan over medium-low heat.
Stir constantly until thickened, about 8–10 minutes. Remove from heat and whisk in butter pieces until smooth.
Pour warm curd into the baked tart shell. Refrigerate for at least 2 hours until set.
Arrange fresh blueberries over the set curd.
Warm apricot jam with 1 tsp water and brush over blueberries for a shiny glaze. Slice and serve chilled.',
  'Blueberry',
  45,
  8,
  'https://images.unsplash.com/photo-1519915028121-7d3463d20b13?w=600&q=80'
);