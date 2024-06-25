<?php
class FPTree {
    public $root;
    public $headerTable;

    public function __construct() {
        $this->root = new FPNode(null, null);
        $this->headerTable = [];
    }

    public function addTransaction($transaction) {
        $sortedItems = $this->sortItems($transaction);
        $this->insertTree($sortedItems, $this->root);
    }

    private function sortItems($transaction) {
        // Urutkan item berdasarkan frekuensi (frekuensi dapat diperoleh dari header table)
        usort($transaction, function($a, $b) {
            return $this->headerTable[$b]['frequency'] - $this->headerTable[$a]['frequency'];
        });
        return $transaction;
    }

    private function insertTree($items, $node) {
        if (count($items) == 0) return;

        $first = $items[0];
        $child = $node->getChild($first);

        if ($child == null) {
            $child = new FPNode($first, $node);
            $node->addChild($child);

            if (!isset($this->headerTable[$first])) {
                $this->headerTable[$first] = ['frequency' => 0, 'nodes' => []];
            }
            $this->headerTable[$first]['nodes'][] = $child;
        }

        $child->incrementFrequency();
        array_shift($items);
        $this->insertTree($items, $child);
    }

    public function buildHeaderTable($transactions) {
        foreach ($transactions as $transaction) {
            foreach ($transaction as $item) {
                if (!isset($this->headerTable[$item])) {
                    $this->headerTable[$item] = ['frequency' => 0, 'nodes' => []];
                }
                $this->headerTable[$item]['frequency']++;
            }
        }
    }

    public function minePatterns($minSupport) {
        $patterns = [];
        foreach ($this->headerTable as $item => $entry) {
            $pattern = $this->minePatternBase($item, $minSupport);
            if (!empty($pattern)) {
                $patterns[$item] = $pattern;
            }
        }
        return $patterns;
    }

    private function minePatternBase($item, $minSupport) {
        $patterns = [];
        foreach ($this->headerTable[$item]['nodes'] as $node) {
            $frequency = $node->frequency;
            $path = $node->getPath();
            if ($frequency >= $minSupport) {
                $patterns[] = ['pattern' => $path, 'frequency' => $frequency];
            }
        }
        return $patterns;
    }
}

class FPNode {
    public $item;
    public $frequency;
    public $parent;
    public $children;

    public function __construct($item, $parent) {
        $this->item = $item;
        $this->frequency = 1;
        $this->parent = $parent;
        $this->children = [];
    }

    public function getChild($item) {
        foreach ($this->children as $child) {
            if ($child->item == $item) {
                return $child;
            }
        }
        return null;
    }

    public function addChild($child) {
        $this->children[] = $child;
    }

    public function incrementFrequency() {
        $this->frequency++;
    }

    public function getPath() {
        $path = [];
        $node = $this;
        while ($node->parent != null) {
            $path[] = $node->item;
            $node = $node->parent;
        }
        return array_reverse($path);
    }
}
?>
