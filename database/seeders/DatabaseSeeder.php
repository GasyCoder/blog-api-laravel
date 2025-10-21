<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer les utilisateurs
        $superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@blog.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'bio' => 'Administrateur principal du blog',
            'email_verified_at' => now(),
        ]);

        $writer1 = User::create([
            'name' => 'Marie Dupont',
            'email' => 'writer1@blog.com',
            'password' => Hash::make('password'),
            'role' => 'writer',
            'bio' => 'Passionnée de technologie et développement web',
            'email_verified_at' => now(),
        ]);

        $writer2 = User::create([
            'name' => 'Pierre Martin',
            'email' => 'writer2@blog.com',
            'password' => Hash::make('password'),
            'role' => 'writer',
            'bio' => 'Expert en cybersécurité et DevOps',
            'email_verified_at' => now(),
        ]);

        $user = User::create([
            'name' => 'Jean Lecteur',
            'email' => 'user@blog.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'bio' => 'Amateur de tech et de lecture',
            'email_verified_at' => now(),
        ]);

        // Créer des catégories
        $categories = [
            ['name' => 'Développement Web', 'slug' => 'developpement-web', 'description' => 'Tout sur le développement web moderne', 'color' => '#3B82F6', 'order' => 1],
            ['name' => 'DevOps', 'slug' => 'devops', 'description' => 'CI/CD, conteneurisation et automatisation', 'color' => '#10B981', 'order' => 2],
            ['name' => 'Sécurité', 'slug' => 'securite', 'description' => 'Cybersécurité et bonnes pratiques', 'color' => '#EF4444', 'order' => 3],
            ['name' => 'JavaScript', 'slug' => 'javascript', 'description' => 'Le langage du web', 'color' => '#F59E0B', 'order' => 4],
            ['name' => 'Cloud', 'slug' => 'cloud', 'description' => 'Cloud computing et infrastructure', 'color' => '#8B5CF6', 'order' => 5],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Créer des tags
        $tagNames = [
            'Laravel', 'PHP', 'React', 'Vue.js', 'Next.js', 'TypeScript',
            'Docker', 'Kubernetes', 'AWS', 'Azure', 'Git', 'GitHub',
            'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'API', 'REST',
            'GraphQL', 'Testing', 'Performance', 'SEO', 'UI/UX', 'Design'
        ];

        $tags = collect($tagNames)->map(fn($name) => Tag::create(['name' => $name]));

        // Créer des posts
        $postsData = [
            [
                'user' => $writer1,
                'category_id' => 1,
                'title' => 'Introduction à Laravel 12 : Les nouveautés',
                'excerpt' => 'Découvrez les nouvelles fonctionnalités de Laravel 12 et comment elles peuvent améliorer vos projets.',
                'content' => $this->getLongContent('Laravel 12'),
                'tags' => [0, 1], // Laravel, PHP
                'status' => 'published',
            ],
            [
                'user' => $writer1,
                'category_id' => 4,
                'title' => 'Next.js 14 : Le guide complet',
                'excerpt' => 'Tout ce que vous devez savoir sur Next.js 14 et le nouveau App Router.',
                'content' => $this->getLongContent('Next.js'),
                'tags' => [2, 4, 5], // React, Next.js, TypeScript
                'status' => 'published',
            ],
            [
                'user' => $writer2,
                'category_id' => 2,
                'title' => 'Docker en production : Les bonnes pratiques',
                'excerpt' => 'Comment utiliser Docker efficacement en production avec des exemples concrets.',
                'content' => $this->getLongContent('Docker'),
                'tags' => [6, 7], // Docker, Kubernetes
                'status' => 'published',
            ],
            [
                'user' => $writer2,
                'category_id' => 3,
                'title' => 'Sécuriser votre API REST : Guide complet',
                'excerpt' => 'Les meilleures pratiques pour sécuriser vos APIs REST contre les attaques courantes.',
                'content' => $this->getLongContent('API Security'),
                'tags' => [16, 17], // API, REST
                'status' => 'published',
            ],
            [
                'user' => $writer1,
                'category_id' => 5,
                'title' => 'AWS vs Azure : Quel cloud choisir en 2025 ?',
                'excerpt' => 'Comparatif détaillé des deux géants du cloud computing.',
                'content' => $this->getLongContent('Cloud'),
                'tags' => [8, 9], // AWS, Azure
                'status' => 'published',
            ],
            [
                'user' => $writer1,
                'category_id' => 1,
                'title' => 'Créer une API RESTful avec Laravel',
                'excerpt' => 'Tutorial pas à pas pour construire une API complète.',
                'content' => $this->getLongContent('Laravel API'),
                'tags' => [0, 1, 16], // Laravel, PHP, API
                'status' => 'draft',
            ],
        ];

        foreach ($postsData as $postData) {
            $post = Post::create([
                'user_id' => $postData['user']->id,
                'category_id' => $postData['category_id'],
                'title' => $postData['title'],
                'excerpt' => $postData['excerpt'],
                'content' => $postData['content'],
                'status' => $postData['status'],
                'published_at' => $postData['status'] === 'published' ? now()->subDays(rand(1, 30)) : null,
            ]);

            $post->tags()->attach(collect($postData['tags'])->map(fn($i) => $tags[$i]->id));

            // Ajouter des commentaires aux posts publiés
            if ($post->status === 'published') {
                for ($i = 0; $i < rand(2, 5); $i++) {
                    $comment = Comment::create([
                        'post_id' => $post->id,
                        'user_id' => $user->id,
                        'content' => $this->getCommentContent(),
                        'status' => rand(0, 1) ? 'approved' : 'pending',
                        'approved_at' => rand(0, 1) ? now() : null,
                        'approved_by' => rand(0, 1) ? $postData['user']->id : null,
                    ]);

                    // Ajouter des réponses
                    if (rand(0, 1)) {
                        Comment::create([
                            'post_id' => $post->id,
                            'user_id' => $postData['user']->id,
                            'parent_id' => $comment->id,
                            'content' => "Merci pour ton commentaire ! Content que l'article t'ait plu.",
                            'status' => 'approved',
                            'approved_at' => now(),
                            'approved_by' => $postData['user']->id,
                        ]);
                    }
                }
            }
        }
    }

    private function getLongContent(string $topic): string
    {
        return <<<EOT
# Introduction

Cet article explore en profondeur le sujet de {$topic}. Nous allons voir ensemble les concepts fondamentaux, les bonnes pratiques et des exemples concrets d'implémentation.

## Concepts de base

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.

### Point important 1

Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.

### Point important 2

Sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.

## Mise en pratique

Totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit.

```javascript
// Exemple de code
const example = () => {
  console.log('Hello World');
  return true;
};
```

## Conclusion

Sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit.

EOT;
    }

    private function getCommentContent(): string
    {
        $comments = [
            "Excellent article ! Très instructif et bien expliqué.",
            "Merci pour ce partage, ça m'a beaucoup aidé.",
            "Super contenu, j'attends la suite avec impatience !",
            "Très intéressant, je ne connaissais pas cette approche.",
            "Article de qualité, merci pour le temps passé à l'écrire.",
        ];

        return $comments[array_rand($comments)];
    }
}