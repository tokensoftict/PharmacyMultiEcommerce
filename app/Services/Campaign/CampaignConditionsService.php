<?php

namespace App\Services\Campaign;

use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * CampaignConditionsService
 *
 * Evaluates a JSON condition tree against a User + context payload.
 *
 * Condition tree format:
 * {
 *   "operator": "AND",
 *   "rules": [
 *     { "field": "order_count", "operator": ">=", "value": 3 },
 *     { "field": "cart_total",  "operator": ">",  "value": 5000 },
 *     {
 *       "operator": "OR",
 *       "rules": [
 *         { "field": "store_type", "operator": "==", "value": "wholesale" },
 *         { "field": "member_group", "operator": "in", "value": [1, 2] }
 *       ]
 *     }
 *   ]
 * }
 *
 * Supported fields:
 *   order_count, order_total_lifetime, cart_total, cart_item_count,
 *   store_type, member_group, days_since_signup, days_since_last_order,
 *   loyalty_points, platform, app_version
 *
 * Supported operators: ==, !=, >, >=, <, <=, in, not_in, contains
 */
class CampaignConditionsService
{
    /**
     * Evaluate a condition tree.
     */
    public function evaluate(array $tree, User $user, array $context = []): bool
    {
        try {
            return $this->evaluateNode($tree, $user, $context);
        } catch (\Throwable $e) {
            Log::warning("[CampaignConditions] Evaluation error for user #{$user->id}: {$e->getMessage()}");
            return true; // Fail-open — don't silently suppress campaigns on evaluation errors
        }
    }

    private function evaluateNode(array $node, User $user, array $context): bool
    {
        // Group node: has operator AND/OR and a rules array
        if (isset($node['operator']) && isset($node['rules'])) {
            $results = array_map(
                fn($rule) => $this->evaluateNode($rule, $user, $context),
                $node['rules']
            );

            return match (strtoupper($node['operator'])) {
                'AND' => !in_array(false, $results, true),
                'OR'  => in_array(true, $results, true),
                default => true,
            };
        }

        // Leaf rule node: has field + operator + value
        if (isset($node['field'])) {
            return $this->evaluateRule($node['field'], $node['operator'], $node['value'], $user, $context);
        }

        return true;
    }

    private function evaluateRule(string $field, string $op, mixed $expected, User $user, array $context): bool
    {
        $actual = $this->resolveField($field, $user, $context);
        if ($actual === null) return true; // unknown field → pass

        return match ($op) {
            '=='      => $actual == $expected,
            '!='      => $actual != $expected,
            '>'       => $actual > $expected,
            '>='      => $actual >= $expected,
            '<'       => $actual < $expected,
            '<='      => $actual <= $expected,
            'in'      => in_array($actual, (array) $expected),
            'not_in'  => !in_array($actual, (array) $expected),
            'contains' => str_contains((string) $actual, (string) $expected),
            default   => true,
        };
    }

    private function resolveField(string $field, User $user, array $context): mixed
    {
        return match ($field) {
            'order_count'            => $user->orders()->count(),
            'order_total_lifetime'   => $user->orders()->sum('order_total') ?? 0,
            'cart_total'             => $context['cart_total'] ?? 0,
            'cart_item_count'        => $context['cart_item_count'] ?? 0,
            'store_type'             => $context['store_type'] ?? null,
            'member_group'           => $user->member_group_id ?? $user->retail_member_group_id ?? null,
            'days_since_signup'      => $user->created_at ? now()->diffInDays($user->created_at) : null,
            'days_since_last_order'  => $this->daysSinceLastOrder($user),
            'loyalty_points'         => $user->loyalty_points ?? 0,
            'platform'               => $context['platform'] ?? null,
            'app_version'            => $context['app_version'] ?? null,
            default                  => null,
        };
    }

    private function daysSinceLastOrder(User $user): ?int
    {
        $lastOrder = $user->orders()->latest()->first();
        if (!$lastOrder) return null;
        return now()->diffInDays($lastOrder->created_at);
    }
}
