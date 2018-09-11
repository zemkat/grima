<?php
/**
 * @file
 * @brief {@link zemowsplat::Splat Splat} and {@link zemowsplat::SplatExecutionContext helper} - low tech template engine
 */

namespace zemowsplat;

/**
 * @brief Simple template engine
 */
class Splat {
    private $paths;
    private $bases;
/* the stack */
    private $stack;
    /**
     * @brief push a scope onto the stack
     * @param array $scope - the new scope to be merged with old scope
     */
    protected function push( array $scope ) { 
        $this->stack[] = array_merge( (array) $this->peek(), (array) $scope );
    }

    /**
     * @brief peek at current scope
     * @return array $scope - the current scope
     */
    protected function peek() {
        $length = sizeof( $this->stack );
        return $length ? $this->stack[ $length - 1 ] : array();
    }

    /**
     * @brief pop a scope off the stack
     * @return array $scope - the former scope
     */
    protected function pop() {
        return array_pop( $this->stack );
    }

    /**
     * @brief Setup default scopes and paths, but leave base dirs empty
     */
    public function __construct() {
        $this->stack = [ [
            'e' =>  'htmlspecialchars',
            't' =>  [ $this, 'splat' ],
        ] ];
        $this->paths = [];
        $this->bases = [];
    }

    /**
     * @brief Main function: execute template `$name` with given `$scope`
     * @details
     * #### Default variables
     * There are two convenience variables:
     * * `$e = htmlspecialchars` - suitable for
     * ```php
     * <div class="<?=$e($className)?>"><?=$e($textContent)?></div>
     * ```
     * * `$t = $this->splat` - suitable for calling more templates.
     *
     * #### Suggested template contents
     * Technically any PHP code is valid in the template, but it is intended to be
     * used on text/html heavy templates with some:
     * ```php
     * <?=$e($var)?>
     * ```
     * and some
     * ```php
     * <?php if(isset($var)): ?> ... <?php else: ?> ... <?php endif ?>
     * ```
     * and some
     * ```php
     * <?php foreach($var as $key=>$val): ?> ... <?php endforeach ?>
     * ```
     * with some
     * ```php
     * <?=$t('subtemplate',['var'=>$val])?>
     * ```
     * and that's basically it.
     * @param string $name - name of the template to execute
     * @param array $scope - additional variables in scope of the template
     */
    public function splat( $name, $scope = array() ) {
        $this->push( $scope );
        $scope = $this->peek();
        foreach( $this->nameToPaths( $name ) as $file ) {
            $exe = new SplatExecutionContext( $file, $scope );
            $exe->splat();
        }
        $this->pop();
    }

    /**
     * @brief Add a resolution of `$name` to `$relativePath`
     * @param array $paths - associative array of `$name => $path` pairs to setup path resolution
     */
    public function addPaths( array $paths ) {
        foreach( $paths as $name => $path ) {
            $this->paths[$name][] = $path;
        }
    }

    /**
     * @brief Add a base directory to resolve `$relativePath` to `$absolutePath`
     * @param string|array $bases - beginnings of absolute paths to search for relative paths
     */
    public function addBases( $bases ) {
        foreach( (array) $bases as $base ) {
            $this->bases[] = $base;
        }
    }

    /**
     * @brief Resolve a template `$name` to an array of absolute `$path`s
     * @details
     * In case of additions, the array may be long, but is usually a single
     * path to be `include`d in order to print the template.
     * @param string $name - name of the template to lookup
     * @return array $paths - absolute paths that should be included in order to print the template
     */
    protected function nameToPaths($name) {
        $ret = array();
        @$onlyOne = array_merge( (array) $this->paths[$name], [ "$name.php", ]);
        foreach( $onlyOne as $file ) {
            foreach( $this->bases as $base ) {
                $path = $base . DIRECTORY_SEPARATOR . $file;
                if( is_readable( $path ) ) {
                    $ret[] = $path;
                    break 2;
                }
            }
        }
        @$adds = array_merge( (array) $this->paths[$name.'[]'], [ "$name-add.php", ]);
        foreach( $adds as $file ) {
            foreach( $this->bases as $base ) {
                $path = $base . DIRECTORY_SEPARATOR . $file;
                if( is_readable( $path ) ) {
                    $ret[] = $path;
                }
            }
        }
        return $ret;
    }
}

/**
 * @brief Helper class for {@link Splat} that runs the template PHP
 * @details In order to keep the variable scope for the template clean,
 * we have a very simple class that keeps track of what file to execute
 * in what scope. It sets up the scope, and includes the file.
 */
class SplatExecutionContext {
    protected $file;
    protected $scope;
    /**
     * @brief Record the `$file` and `$scope`
     * @param string $file - The PHP file to execute via `include`
     * @param array $scope - The associative array to use as the scope via `extract`
     */
    public function __construct( $file, array $scope ) {
        $this->file  = $file;
        $this->scope = $scope;
    }

    /**
     * @brief `extract` the scope and `include` the file
     */
    public function splat() {
        extract($this->scope);
        include $this->file;
    }
}
