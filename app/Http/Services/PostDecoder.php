<?php

namespace App\Http\Services;

use Carbon\Carbon;

class PostDecoder
{
    public function instagramDecoder($json, $nodeName, $cutTimestamp)
    {
        // Verifica se os dados estão no formato esperado

        if($nodeName == 'edges') {
            if (!isset($json->edges))
                return null; // Retorna nulo caso o JSON não esteja no formato esperado
        } else {
            if (!isset($json->posts))
                return null; // Retorna nulo caso o JSON não esteja no formato esperado
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

        foreach ($json->edges as $post) {
            $node = $post->node;

            if($node->taken_at_timestamp < $timestamp)
                continue;

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

            // Converte o timestamp para um formato legível
            $dateTime = Carbon::createFromTimestamp($node->taken_at_timestamp)->toDateTimeString();

            // Extrai a legenda (se houver)
            $caption = $node->edge_media_to_caption->edges[0]->node->text ?? null;

            $tags = '';
            if (preg_match_all('/#\w+/', $caption, $matches)) {
                foreach ($matches[0] as $word) {
                    $tags .= $word . ', ';
                }
                // Remove a última vírgula e espaço no final
                $tags = rtrim($tags, ', ');
            }

            $mentions = '';
            if (count($node->edge_media_to_tagged_user->edges) > 0) {
                foreach ($node->edge_media_to_tagged_user->edges as $mention) {
                    $mentions .= $mention->node->user->username . ', ';
                }
            }

            // Monta o JSON limpo
            $cleanedPosts[] = (object)[
                'id' => $node->id,
                'shortcode' => $node->shortcode,
                'type' => $type,
                'dateTime' => $dateTime,
                'caption' => $caption,
                'mentions' => $mentions,
                'tags' => $tags,
                'num_likes' => $node->edge_media_preview_like->count ?? 0,
                'num_comments' => $node->edge_media_to_comment->count ?? 0,
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
            return null; // Retorna nulo caso o JSON não esteja no formato esperado
        }

        $comments = [];

        foreach ($json->edges as $post) {
            $node = $post->node;

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
                    'id' => $node->owner->id,
                    'name' => $node->owner->username,
                ],
                'likes' => $node->edge_liked_by->count,
            ];
        }

        return $comments;
    }
}
