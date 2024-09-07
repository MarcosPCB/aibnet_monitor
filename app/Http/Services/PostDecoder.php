<?php

namespace App\Http\Services;

use Carbon\Carbon;

class PostDecoder
{
    public function instagramDecoder($json, $nodeName, $cutTimestamp)
    {
        // Verifica se os dados estão no formato esperado

        $nodes = null;

        if($nodeName == 'edges') {
            if (!isset($json->edges))
                return null; // Retorna nulo caso o JSON não esteja no formato esperado
            $nodes = $json->edges;
        } else if($nodeName == 'posts') {
            if (!isset($json->posts))
                return null; // Retorna nulo caso o JSON não esteja no formato esperado
            $nodes = $json->posts;
        } else if($nodeName == 'items') {
            if (!isset($json->items))
                return null; // Retorna nulo caso o JSON não esteja no formato esperado
            $nodes = $json->items;
        }

        $timestamp = 0;

        switch($cutTimestamp) {
            case 'today':
                $timestamp = Carbon::today()->timestamp;
                break;

            case 'week':
                $timestamp = Carbon::now()->startOfWeek(Carbon::SUNDAY)->timestamp;
                break;

            case 'last-week':
                $timestamp = Carbon::now()->subWeek()->startOfWeek(Carbon::SUNDAY)->timestamp;
                break;

            case 'month':
                $timestamp = Carbon::now()->startOfMonth()->timestamp;
                break;

            case 'year':
                $timestamp = Carbon::now()->startOfYear()->timestamp;
                break;
        }

        $cleanedPosts = [];

        foreach ($nodes as $post) {
            if($nodeName != 'items')
                $node = $post->node;
            else $node = $post;

            $dateTime = null;

            if(isset($node->taken_at_timestamp)) {
                $dateTime = Carbon::parse($node->taken_at_timestamp);
                if($node->taken_at_timestamp < $timestamp)
                    continue;
            } else {
                $dateTime = Carbon::createFromTimestamp($node->taken_at)->toDateTimeString();
                if($node->taken_at < $timestamp)
                    continue;
            }

            if(isset($node->__typename)) {
                // Determina o tipo de postagem
                switch ($node->__typename) {
                    case 'GraphImage':
                        $type = 'image';
                        break;
                    case 'GraphVideo':
                        $type = 'video';
                        break;
                    case 'GraphStory':
                        $type = 'story';
                        break;
                    case 'GraphSidecar':
                        $type = 'carousel';
                        break;
                    default:
                        $type = 'unknown';
                }
            } else {
                switch ($node->product_type) {
                    case 'feed':
                        $type = 'image';
                        break;
                    case 'clips':
                        $type = 'video';
                        break;
                    default:
                        $type = 'unknown';
                }
            }

            // Extrai a legenda (se houver)
            $caption = isset($node->edge_media_to_caption)
                ? ($node->edge_media_to_caption->edges[0]->node->text ?? null)
                : (isset($node->caption) ? $node->caption->text : null);

            $tags = '';
            if (preg_match_all('/#\w+/', $caption, $matches)) {
                foreach ($matches[0] as $word) {
                    $tags .= $word . ', ';
                }
                // Remove a última vírgula e espaço no final
                $tags = rtrim($tags, ', ');
            }

            $mentions = '';
            if(isset($node->edge_media_to_tagged_user)) {
                if (count($node->edge_media_to_tagged_user->edges) > 0) {
                    foreach ($node->edge_media_to_tagged_user->edges as $mention) {
                        $mentions .= $mention->node->user->username . ', ';
                    }
                }
            } else {
                if (count($node->usertags) > 0) {
                    foreach ($node->usertags as $mention) {
                        $mentions .= $mention->user->username . ', ';
                    }
                }
            }

            // Monta o JSON limpo
            $cleanedPosts[] = (object)[
                'id' => $node->id,
                'shortcode' => $node->shortcode ?? $node->code ?? null,
                'type' => $type,
                'dateTime' => $dateTime,
                'caption' => $caption,
                'mentions' => $mentions,
                'tags' => $tags,
                'num_likes' => $node->edge_media_preview_like->count ?? $node->like_count ?? 0,
                'num_comments' => $node->edge_media_to_comment->count ?? $node->comment_count ?? 0,
                'comments' => array()
            ];
        }

        return (object)[
            'count' => count($cleanedPosts),
            'posts' => $cleanedPosts
        ];
    }

    public function instagramCommentDecoder($json) {
        // Verifica se os dados estão no formato esperado
        if (!isset($json->edges)) {
            if(isset($json->comments))
                $json = $json->comments;
            else return null; // Retorna nulo caso o JSON não esteja no formato esperado
        } else  $json = $json->edges;

        $comments = [];

        foreach ($json as $post) {
            if(isset($post->node))
                $node = $post->node;
            else $node = $post;

            $tags = '';
            if (preg_match_all('/#\w+/', $node->text, $matches)) {
                foreach ($matches[0] as $word) {
                    $tags .= $word . ', ';
                }
                // Remove a última vírgula e espaço no final
                $tags = rtrim($tags, ', ');
            }

            $comments[] = (object)[
                'id' => $node->id,
                'text' => $node->text,
                'tags' => $tags,
                'author' => [
                    'id' => $node->owner->id ?? $node->user->id ?? null,
                    'name' => $node->owner->username ?? $node->user->username ?? null,
                ],
                'likes' => $node->edge_liked_by->count ?? $node->like_count ?? 0,
            ];
        }

        return $comments;
    }
}
